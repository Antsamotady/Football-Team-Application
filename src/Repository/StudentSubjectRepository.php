<?php

namespace App\Repository;

use App\Entity\StudentSubject;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method StudentSubject|null find($id, $lockMode = null, $lockVersion = null)
 * @method StudentSubject|null findOneBy(array $criteria, array $orderBy = null)
 * @method StudentSubject[]    findAll()
 * @method StudentSubject[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StudentSubjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StudentSubject::class);
    }

    /**
     * @return StudentSubject[] Returns an array of StudentSubject objects
     */
    public function findByStudent($student)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.student = :val')
            ->setParameter('val', $student)
            ->orderBy('s.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /*
    public function findOneBySomeField($value): ?StudentSubject
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
