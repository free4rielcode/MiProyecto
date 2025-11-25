<?php

namespace App\Controller;

use App\Repository\ProductoRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class InventarioController extends AbstractController
{
    #[Route('/inventario', name: 'app_inventario')]
    public function index(ProductoRepository $productoRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
            $productos = $productoRepository->findAll();

        
        return $this->render('inventario/index.html.twig', [
            'productos' => $productos,
            //'controller_name' => 'InventarioController',
        ]);
    }
}
