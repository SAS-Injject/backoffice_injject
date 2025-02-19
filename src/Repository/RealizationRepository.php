<?php

namespace App\Repository;

use App\Entity\Realization;
use App\Traits\EntityFindAllPaginatedTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Realization>
 */
class RealizationRepository extends ServiceEntityRepository
{
  use EntityFindAllPaginatedTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Realization::class);
    }

    //    /**
    //     * @return Realization[] Returns an array of Realization objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Realization
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
