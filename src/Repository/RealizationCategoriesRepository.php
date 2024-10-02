<?php

namespace App\Repository;

use App\Entity\RealizationCategories;
use App\Traits\EntityFindAllPaginatedTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RealizationCategories>
 */
class RealizationCategoriesRepository extends ServiceEntityRepository
{
  use EntityFindAllPaginatedTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RealizationCategories::class);
    }

    //    /**
    //     * @return RealizationCategories[] Returns an array of RealizationCategories objects
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

    //    public function findOneBySomeField($value): ?RealizationCategories
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
