<?php

namespace App\Repository;

use App\Entity\DetalleVenta;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DetalleVenta>
 */
class DetalleVentaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DetalleVenta::class);
    }
    // src/Repository/DetalleVentaRepository.php
    public function getProductosMasVendidos(int $limit = 5): array
    {
        return $this->createQueryBuilder('dv')
            ->select('p.nombre as producto, SUM(dv.cantidad) as vendidos, p.stock as stock')
            ->join('dv.producto', 'p')
            ->groupBy('p.id')
            ->orderBy('vendidos', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }


    //    /**
    //     * @return DetalleVenta[] Returns an array of DetalleVenta objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('d.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?DetalleVenta
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
