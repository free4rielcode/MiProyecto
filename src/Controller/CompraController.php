<?php

namespace App\Controller;

use App\Entity\Compra;
use App\Entity\Producto;
use App\Form\CompraType;
use App\Entity\DetalleCompra;
use App\Repository\CompraRepository;
use App\Repository\ProductoRepository;
use App\Repository\CategoriaRepository;
use App\Repository\ProveedorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class CompraController extends AbstractController
{
    #[Route('/compra', name: 'app_compra')]
    public function index(CompraRepository $compraRepository): Response
    {
        $compras = $compraRepository->findBy([], ['fecha' => 'DESC']);
        return $this->render('compra/index.html.twig', [
            'compras' => $compras,
        ]);
    }

    #[Route('/compra/registrar', name: 'compra_registrar', methods: ['POST'])]
    public function registrarCompra(
        Request $request,
        EntityManagerInterface $em,
        ProductoRepository $productoRepo,
        CategoriaRepository $categoriaRepo,
        ProveedorRepository $proveedorRepo
    ): Response {
        $payload = json_decode($request->getContent(), true);
        if (!$payload) {
            return $this->json(['success' => false, 'error' => 'Datos inválidos'], 400);
        }

        $proveedorId = $payload['proveedor_id'] ?? null;
        $items = $payload['productos'] ?? [];

        if (!$proveedorId || empty($items)) {
            return $this->json(['success' => false, 'error' => 'Proveedor e ítems son requeridos'], 400);
        }

        $proveedor = $proveedorRepo->find($proveedorId);
        if (!$proveedor) {
            return $this->json(['success' => false, 'error' => 'Proveedor no encontrado'], 404);
        }

        $compra = new Compra();
        $compra->setFecha(new \DateTime());
        $compra->setUsuario($this->getUser());
        $compra->setProveedor($proveedor);

        $total = 0;

        foreach ($items as $item) {
            $categoriaId = $item['categoria_id'] ?? null;
            $productoId = $item['producto_id'] ?? null;
            $productoNombre = $item['productoNombre'] ?? null;
            $productoDescripcion = $item['productoDescripcion'] ?? null;
            $cantidad = (int) ($item['cantidad'] ?? 0);
            $precioUnitario = (float) ($item['precio_unitario'] ?? 0);

            if (!$categoriaId || $cantidad <= 0 || $precioUnitario <= 0) {
                return $this->json(['success' => false, 'error' => 'Datos incompletos en producto'], 400);
            }

            $categoria = $categoriaRepo->find($categoriaId);
            if (!$categoria) {
                return $this->json(['success' => false, 'error' => 'Categoría no encontrada'], 404);
            }

            // Caso 1: producto existente
            if ($productoId) {
                $producto = $productoRepo->find($productoId);
                if (!$producto) {
                    return $this->json(['success' => false, 'error' => 'Producto no encontrado'], 404);
                }
            } else {
                // Caso 2: nuevo producto
                if (!$productoNombre) {
                    return $this->json(['success' => false, 'error' => 'Debe ingresar nombre para nuevo producto'], 400);
                }
                $producto = new Producto();
                $producto->setNombre($productoNombre);
                $producto->setDescripcion($productoDescripcion);
                $producto->setCategoria($categoria);
                $producto->setPrecio($precioUnitario);
                $producto->setPrecioVenta($precioUnitario);
                $producto->setStock(0);
                $em->persist($producto);
            }

            // En compras → se **incrementa** el stock
            $producto->setStock($producto->getStock() + $cantidad);

            // Detalle de compra
            $detalle = new DetalleCompra();
            $detalle->setCompra($compra);
            $detalle->setProducto($producto);
            $detalle->setCategoria($categoria);
            $detalle->setCantidad($cantidad);
            $detalle->setPrecioUnitario($precioUnitario);
            $detalle->setSubtotal($cantidad * $precioUnitario);

            $total += $detalle->getSubtotal();

            $em->persist($producto);
            $em->persist($detalle);
        }

        $compra->setTotal($total);
        $em->persist($compra);
        $em->flush();

        return $this->json([
            'success' => true,
            'compra_id' => $compra->getId(),
        ]);
    }

    #[Route('/productos-por-categoria/{id}', name: 'productos_por_categoria', methods: ['GET'])]
    public function productosPorCategoria(
        int $id,
        EntityManagerInterface $em
    ): Response {
        $productos = $em->getRepository(Producto::class)->findBy(['categoria' => $id]);

        $data = [];

        foreach ($productos as $producto) {
            $data[] = [
                'id' => $producto->getId(),
                'nombre' => $producto->getNombre(),
                'precio_venta' => $producto->getPrecioVenta(),
            ];
        }

        // Siempre incluir la opción "nuevo producto"
        $data[] = [
            'id' => 'nuevo',
            'nombre' => '➕ Nuevo producto',
            'precio_venta' => null,
        ];

        return $this->json($data);
    }



    #[Route('/compra/nueva', name: 'compra_nueva')]
    public function nuevaCompra(Request $request, EntityManagerInterface $em, CategoriaRepository $categoriaRepository): Response
    {
        $compra = new Compra();
        $compra->addDetalleCompra(new DetalleCompra());
        $form = $this->createForm(CompraType::class, $compra);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Set productoNombre and productoDescripcion from form data
            $detalleComprasData = $request->request->all()['compra']['detalleCompras'] ?? [];
            foreach ($compra->getDetalleCompras() as $index => $detalle) {
                if (isset($detalleComprasData[$index]['productoNombre'])) {
                    $detalle->setProductoNombre($detalleComprasData[$index]['productoNombre']);
                }
                if (isset($detalleComprasData[$index]['productoDescripcion'])) {
                    $detalle->setProductoDescripcion($detalleComprasData[$index]['productoDescripcion']);
                }
            }

            $compra->setFecha(new \DateTime());
            $compra->setUsuario($this->getUser());

            $total = 0;
            foreach ($compra->getDetalleCompras() as $detalle) {
                $detalle->setCompra($compra);
                $detalle->setSubtotal($detalle->getCantidad() * $detalle->getPrecioUnitario());

                // CREA O BUSCA EL PRODUCTO Y ACTUALIZA SU STOCK
                $categoria = $detalle->getCategoria();
                $productoNombre = $detalle->getProductoNombre();
                $productoDescripcion = $detalle->getProductoDescripcion();

                if ($categoria && $productoNombre) {
                    $producto = $em->getRepository(Producto::class)->findOneBy(['nombre' => $productoNombre, 'categoria' => $categoria]);
                    if (!$producto) {
                        $producto = new Producto();
                        $producto->setNombre($productoNombre);
                        $producto->setCategoria($categoria);
                        $producto->setStock(0);
                        $producto->setPrecio($detalle->getPrecioUnitario());
                        $producto->setPrecioVenta($detalle->getPrecioUnitario() * 1); // Ejemplo de precio de venta
                        $producto->setDescripcion($productoDescripcion); // Usar la descripción del formulario
                        $em->persist($producto);
                    }
                    $producto->setStock($producto->getStock() + $detalle->getCantidad());
                    $detalle->setProducto($producto);
                    $em->persist($producto);
                }

                $total += $detalle->getSubtotal();
                $em->persist($detalle);
            }

            $compra->setTotal($total);

            $em->persist($compra);
            $em->flush();

            $this->addFlash('success', 'Compra registrada correctamente.');
            return $this->redirectToRoute('app_compra');
        }

        // Si mantienes el form Symfony, puedes seguir mostrándolo; si no, solo renderiza la vista
        $categorias = $categoriaRepository->findAll();
        $proveedores = $em->getRepository(\App\Entity\Proveedor::class)->findAll();

        // Carrito sesión (opcional si quieres recuperar lo que se vaya guardando del lado servidor)
        $carrito = $request->getSession()->get('carrito', []);
        $total = array_reduce($carrito, fn($t, $i) => $t + ($i['precio'] * $i['cantidad']), 0.0);

        return $this->render('compra/nueva.html.twig', [
            'categorias' => $categorias,
            'proveedores' => $proveedores,
            'carrito' => $carrito,
            'total' => $total,
        ]);
    }

    #[Route('/compra/{id}/factura', name: 'compra_factura')]
    public function factura(Compra $compra): Response
    {
        $total = 0;
        foreach ($compra->getDetalleCompras() as $detalle) {
            $total += $detalle->getSubtotal();
        }

        return $this->render('compra/factura.html.twig', [
            'compra' => $compra,
            'total' => $total,
        ]);
    }


    #[Route('/compra/eliminar/{id}', name: 'compra_eliminar', methods: ['POST', 'GET'])]
    public function eliminarCompra(int $id, EntityManagerInterface $em): Response
    {
        $compra = $em->getRepository(Compra::class)->find($id);

        if (!$compra) {
            $this->addFlash('danger', 'Compra no encontrada.');
            return $this->redirectToRoute('app_compra');
        }

        // Eliminar detalles asociados si no tienes onDelete="CASCADE"
        foreach ($compra->getDetalleCompras() as $detalle) {
            $em->remove($detalle);
        }

        $em->remove($compra);
        $em->flush();

        $this->addFlash('success', 'Compra eliminada correctamente.');
        return $this->redirectToRoute('app_compra');
    }

    #[Route('/compra/listar', name: 'compra_listar')]
    public function listarCompras(EntityManagerInterface $em): Response
    {
        $compras = $em->getRepository(Compra::class)->findAll();

        return $this->render('compra/listar.html.twig', [
            'compras' => $compras,
        ]);
    }
}
