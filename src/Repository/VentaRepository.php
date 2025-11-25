<?php

namespace App\Repository;

use App\Entity\Venta;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Venta>
 */
class VentaRepository extends ServiceEntityRepository
{
    
    // src/Repository/VentaRepository.php
    public function getTotalesPorMes(): array
    {
        $qb = $this->createQueryBuilder('v')
            ->select("MONTH(v.fecha) as mes, SUM(v.total) as total")
            ->groupBy('mes')
            ->orderBy('mes', 'ASC');

        return $qb->getQuery()->getResult();
    }
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Venta::class);
    }

    public function getTotalVentas(): float
    {
        return (float) $this->createQueryBuilder('v')
            ->select('COALESCE(SUM(v.total), 0)')
            ->getQuery()
            ->getSingleScalarResult();
    }


    //    /**
    //     * @return Venta[] Returns an array of Venta objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('v.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Venta
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
