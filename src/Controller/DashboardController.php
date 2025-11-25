<?php

namespace App\Controller;

use App\Repository\VentaRepository;
use App\Repository\CompraRepository;
use App\Repository\ProductoRepository;
use App\Repository\DetalleVentaRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DashboardController extends AbstractController
{
    #[Route('/api/dashboard-data', name: 'dashboard_data')]
    public function index(CompraRepository $compraRepo, VentaRepository $ventaRepo): JsonResponse
    {
        $compras = $compraRepo->getTotalesPorMes();
        $ventas  = $ventaRepo->getTotalesPorMes();

        $meses = [
            1 => 'Enero',
            2 => 'Febrero',
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto',
            9 => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre'
        ];

        $comprasData = array_map(fn($c) => [
            'mes'   => $meses[$c['mes']],
            'total' => (float) $c['total']
        ], $compras);

        $ventasData = array_map(fn($v) => [
            'mes'   => $meses[$v['mes']],
            'total' => (float) $v['total']
        ], $ventas);

        return new JsonResponse([
            'compras' => $comprasData,
            'ventas'  => $ventasData
        ]);
    }
    #[Route('/api/productos-mas-vendidos', name: 'productos_mas_vendidos')]
    public function productosMasVendidos(DetalleVentaRepository $detalleRepo): JsonResponse
    {
        $productos = $detalleRepo->getProductosMasVendidos(5);

        // Formateamos la respuesta
        $data = array_map(fn($p) => [
            'producto' => $p['producto'],
            'vendidos' => (int) $p['vendidos'],
            'stock'    => (int) $p['stock']
        ], $productos);

        return new JsonResponse($data);
    }
    #[Route('/api/ganancia-neta', name: 'ganancia_neta')]
    public function gananciaNeta(
        VentaRepository $ventaRepo,
        CompraRepository $compraRepo
    ): JsonResponse {
        $totalVentas  = $ventaRepo->getTotalVentas();
        $totalCompras = $compraRepo->getTotalCompras();
        $ganancia     = $totalVentas - $totalCompras;

        return new JsonResponse([
            'ventas'   => $totalVentas,
            'compras'  => $totalCompras,
            'ganancia' => $ganancia
        ]);
    }
    #[Route('/api/productos-bajo-stock', name: 'productos_bajo_stock')]
    public function productosBajoStock(ProductoRepository $productoRepo): JsonResponse
    {
        $productos = $productoRepo->getProductosBajoStock(5);

        $data = array_map(fn($p) => [
            'id'      => $p['id'],
            'nombre'  => $p['nombre'],
            'stock'   => (int) $p['stock']
        ], $productos);

        return new JsonResponse($data);
    }

}
