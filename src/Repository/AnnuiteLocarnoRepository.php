<?php

namespace App\Repository;

use App\Entity\AnnuiteLocarno;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AnnuiteLocarno|null find($id, $lockMode = null, $lockVersion = null)
 * @method AnnuiteLocarno|null findOneBy(array $criteria, array $orderBy = null)
 * @method AnnuiteLocarno[]    findAll()
 * @method AnnuiteLocarno[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnnuiteLocarnoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AnnuiteLocarno::class);
    }

    // /**
    //  * @return AnnuiteLocarno[] Returns an array of AnnuiteLocarno objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AnnuiteLocarno
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
