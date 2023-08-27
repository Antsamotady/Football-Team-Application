<?php

namespace App\Repository;

use App\Entity\ExamLocation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ExamLocation|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExamLocation|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExamLocation[]    findAll()
 * @method ExamLocation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExamLocationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExamLocation::class);
    }

    // /**
    //  * @return ExamLocation[] Returns an array of ExamLocation objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ExamLocation
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
