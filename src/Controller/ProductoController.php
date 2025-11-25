<?php

namespace App\Controller;

use App\Entity\Categoria;
use App\Entity\Producto;
use App\Form\ProductoType;
use App\Form\CategoriaType;
use App\Repository\ProductoRepository;
use App\Repository\CategoriaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/producto')]
final class ProductoController extends AbstractController
{
    #[Route(name: 'app_producto_index', methods: ['GET'])]
    public function index(ProductoRepository $productoRepository, CategoriaRepository $categoriaRepository): Response
    {
        $categorias = $categoriaRepository->findAll();
        $productos = $productoRepository->findAll();
        $stockPorCategoria = [];
        foreach ($productos as $producto) {
            $categoria = $producto->getCategoria();
            if ($categoria) {
                $catId = $categoria->getId();
                if (!isset($stockPorCategoria[$catId])) {
                    $stockPorCategoria[$catId] = 0;
                }
                $stockPorCategoria[$catId] += $producto->getStock();
            }
        }
        return $this->render('producto/index.html.twig', [
            'productos' => $productos,
            'categorias' => $categorias,
            'stockPorCategoria' => $stockPorCategoria,
        ]);
    }
    #[Route('/categoria', name: 'app_producto_categoria', methods: ['GET', 'POST'])]
    public function categoria(Request $request, EntityManagerInterface $entityManager,CategoriaRepository $categoriaRepository): Response
    {
        
        $categoria = new Categoria();
        $form = $this->createForm(CategoriaType::class, $categoria);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($categoria);
            $entityManager->flush();

            return $this->redirectToRoute('app_producto_index', [], Response::HTTP_SEE_OTHER);
        }
        $categorias = $categoriaRepository->findAll();
        return $this->render('producto/categoria.html.twig', [
            'categoria' => $categoria,

            'form' => $form,
             'categorias' => $categorias,
        ]);
    }

    #[Route('/categoria/{id}', name: 'app_categoria_show', methods: ['GET'])]
    public function categoriaShow(Categoria $categoria, ProductoRepository $productoRepository): Response
    {
        $productos = $productoRepository->findBy(['categoria' => $categoria]);
        return $this->render('categoria/show.html.twig', [
            'categoria' => $categoria,
            'productos' => $productos,
        ]);
    }

    #[Route('/categoria/{id}/edit', name: 'app_categoria_edit', methods: ['GET', 'POST'])]
    public function categoriaEdit(Request $request, Categoria $categoria, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategoriaType::class, $categoria);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_producto_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('categoria/edit.html.twig', [
            'categoria' => $categoria,
            'form' => $form,
        ]);
    }

    #[Route('/categoria/{id}', name: 'app_categoria_delete', methods: ['POST'])]
    public function categoriaDelete(Request $request, Categoria $categoria, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$categoria->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($categoria);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_producto_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/new', name: 'app_producto_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $producto = new Producto();
        $form = $this->createForm(ProductoType::class, $producto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($producto);
            $entityManager->flush();

            return $this->redirectToRoute('app_producto_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('producto/new.html.twig', [
            'producto' => $producto,
            'form' => $form,
        ]);
    }


    #[Route('/{id}', name: 'app_producto_show', methods: ['GET'])]
    public function show(Producto $producto): Response
    {
        return $this->render('producto/show.html.twig', [
            'producto' => $producto,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_producto_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Producto $producto, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProductoType::class, $producto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_producto_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('producto/edit.html.twig', [
            'producto' => $producto,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_producto_delete', methods: ['POST'])]
    public function delete(Request $request, Producto $producto, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $producto->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($producto);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_producto_index', [], Response::HTTP_SEE_OTHER);
    }
}
