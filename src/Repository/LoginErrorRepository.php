<?php

namespace App\Repository;

use App\Entity\LoginError;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method LoginError|null find($id, $lockMode = null, $lockVersion = null)
 * @method LoginError|null findOneBy(array $criteria, array $orderBy = null)
 * @method LoginError[]    findAll()
 * @method LoginError[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LoginErrorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LoginError::class);
    }

    // /**
    //  * @return LoginError[] Returns an array of LoginError objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?LoginError
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
