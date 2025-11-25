<?php

namespace App\Controller;

use App\Entity\Venta;
use App\Form\VentaType;
use App\Entity\Producto;
use App\Entity\Categoria;
use App\Entity\DetalleVenta;
use App\Repository\VentaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class VentaController extends AbstractController
{
    #[Route('/venta', name: 'app_venta')]
    public function index(EntityManagerInterface $em,VentaRepository $ventaRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $ventas = $ventaRepository->findBy([], ['fecha' => 'DESC']);


        return $this->render('venta/index.html.twig', [
            'ventas' => $ventas,
        ]);
    }

    #[Route('/venta/nueva', name: 'venta_nueva')]
    public function nuevaVenta(Request $request, EntityManagerInterface $em): Response
    {
        $venta = new Venta();
        $venta->addDetalle(new DetalleVenta());
        $form = $this->createForm(VentaType::class, $venta);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $venta->setFecha(new \DateTime('now', new \DateTimeZone('America/La_Paz')));
            $venta->setUsuario($this->getUser());

            $total = 0;
            foreach ($venta->getDetalles() as $detalle) {
                $detalle->setVenta($venta);

                $producto = $detalle->getProducto();
                if ($producto) {
                    // ASIGNA el precio de venta del producto al detalle (precioUnitario)
                    $detalle->setPrecioUnitario($producto->getPrecioVenta());

                    // Validación de stock antes de descontar
                    if ($producto->getStock() < $detalle->getCantidad()) {
                        // Si no hay stock suficiente, muestra mensaje y cancela la venta
                        $this->addFlash('danger', 'Sin stock suficiente para el producto: ' . $producto->getNombre());
                        return $this->redirectToRoute('venta_nueva');
                    }
                    // Descontar el stock si hay suficiente
                    $producto->setStock($producto->getStock() - $detalle->getCantidad());
                    $em->persist($producto);
                }

                // Calcula el subtotal con el precio de venta actualizado
                $detalle->setSubtotal($detalle->getCantidad() * $detalle->getPrecioUnitario());
                $total += $detalle->getSubtotal();
                $em->persist($detalle);
            }
            $venta->setTotal($total);

            $em->persist($venta);
            $em->flush();

            $this->addFlash('success', 'Venta registrada correctamente.');
            return $this->redirectToRoute('app_venta');
        }

        $productos = $em->getRepository(Producto::class)->findAll();

        return $this->render('venta/nueva.html.twig', [
            'form' => $form->createView(),
            'productos' => $productos,
            'categorias' => $em->getRepository(Categoria::class)->findAll(),
        ]);
    }

    #[Route('/productos-por-categoria/{id}', name: 'productos_por_categoria', methods: ['GET'])]
    public function productosPorCategoria(int $id, EntityManagerInterface $em): Response
    {
        $productos = $em->getRepository(Producto::class)->findBy(['categoria' => $id]);

        $productosArray = [];
        foreach ($productos as $producto) {
            $productosArray[] = [
                'id' => $producto->getId(),
                'nombre' => $producto->getNombre(),
                'precio_venta' => $producto->getPrecioVenta(),
            ];
        }

        return $this->json($productosArray);
    }

    #[Route('/registrar', name: 'venta_registrar', methods: ['POST'])]
    public function registrarVenta(Request $request, EntityManagerInterface $em): Response
    {
        $data = json_decode($request->getContent(), true);

        if (!$data || !isset($data['productos']) || empty($data['productos'])) {
            return $this->json(['error' => 'Datos inválidos'], 400);
        }

        $venta = new Venta();
        $venta->setFecha(new \DateTime('now', new \DateTimeZone('America/La_Paz')));
        $venta->setUsuario($this->getUser());
        $venta->setEstado('activa');
        $total = 0;

        foreach ($data['productos'] as $item) {
            $producto = $em->getRepository(Producto::class)->find($item['producto_id']);
            if (!$producto) {
                return $this->json(['error' => 'Producto no encontrado'], 400);
            }

            if ($producto->getStock() < $item['cantidad']) {
                return $this->json(['error' => 'Sin stock suficiente para el producto: ' . $producto->getNombre()], 400);
            }

            $detalle = new DetalleVenta();
            $detalle->setVenta($venta);
            $detalle->setProducto($producto);
            $detalle->setCantidad($item['cantidad']);
            $detalle->setPrecioUnitario($item['precio_unitario']);
            $detalle->setSubtotal($item['cantidad'] * $item['precio_unitario']);

            $producto->setStock($producto->getStock() - $item['cantidad']);
            $em->persist($producto);

            $total += $detalle->getSubtotal();
            $em->persist($detalle);
        }

        $venta->setTotal($total);

        $em->persist($venta);
        $em->flush();

        return $this->json(['success' => true, 'venta_id' => $venta->getId()]);
    }

    #[Route('listar', name: 'venta_listar')]
    public function listarVentas(EntityManagerInterface $em): Response
    {
        // 1. Obtener todas las ventas
        $ventas = $em->getRepository(Venta::class)->findAll();

        // 2. Pasar las ventas a la vista
        return $this->render('venta/index.html.twig', [
            'ventas' => $ventas,
        ]);
    }
    

    #[Route('/anular/{id}', name: 'venta_anular')]
    public function anularVenta(int $id, EntityManagerInterface $em): Response
    {
        // 1. Buscar la venta
        $venta = $em->getRepository(Venta::class)->find($id);

        if (!$venta) {
            $this->addFlash('danger', 'Venta no encontrada.');
            return $this->redirectToRoute('venta_listar');
        }

        // 2. Marcar como anulada (supón que tienes un campo 'estado')
        $venta->setEstado('anulada'); // Debes tener este campo en la entidad

        // 3. (Opcional) Revertir stock de productos
        // foreach ($venta->getDetalleVentas() as $detalle) {
        //     $producto = $detalle->getProducto();
        //     $producto->setStock($producto->getStock() + $detalle->getCantidad());
        //     $em->persist($producto);
        // }

        $em->persist($venta);
        $em->flush();

        $this->addFlash('success', 'Venta anulada correctamente.');
        return $this->redirectToRoute('venta_listar');
    }

    #[Route('/venta/{id}/factura', name: 'venta_factura')]
    public function factura(Venta $venta): Response
    {
        return $this->render('venta/factura.html.twig', [
            'venta' => $venta,
        ]);
    }

    #[Route('/venta/{id}/factura/pdf', name: 'venta_factura_pdf')]
    public function facturaPdf(Venta $venta, \Knp\Snappy\Pdf $snappy): Response
    {
        $html = $this->renderView('venta/factura.html.twig', [
            'venta' => $venta,
        ]);

        return new Response(
            $snappy->getOutputFromHtml($html),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="factura_' . $venta->getId() . '.pdf"',
            ]
        );
    }
}
