<?php

namespace App\Repository;

use App\Entity\StudentClasse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method StudentClasse|null find($id, $lockMode = null, $lockVersion = null)
 * @method StudentClasse|null findOneBy(array $criteria, array $orderBy = null)
 * @method StudentClasse[]    findAll()
 * @method StudentClasse[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StudentClasseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StudentClasse::class);
    }

    // /**
    //  * @return StudentClasse[] Returns an array of StudentClasse objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?StudentClasse
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
