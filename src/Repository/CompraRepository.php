<?php

namespace App\Repository;

use App\Entity\Compra;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Compra>
 */
class CompraRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Compra::class);
    }

    public function findUltimasVentas(int $limit = 10): array
    {
        return $this->createQueryBuilder('v')
            ->orderBy('v.fecha', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
    // src/Repository/CompraRepository.php
    public function getTotalesPorMes(): array
    {
        $qb = $this->createQueryBuilder('c')
            ->select("MONTH(c.fecha) as mes, SUM(c.total) as total")
            ->groupBy('mes')
            ->orderBy('mes', 'ASC');

        return $qb->getQuery()->getResult();
    }
    public function getTotalCompras(): float
    {
        return (float) $this->createQueryBuilder('c')
            ->select('COALESCE(SUM(c.total), 0)')
            ->getQuery()
            ->getSingleScalarResult();
    }




    //    /**
    //     * @return Compra[] Returns an array of Compra objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Compra
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
