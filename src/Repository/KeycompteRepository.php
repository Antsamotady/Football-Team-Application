<?php

namespace App\Repository;

use App\Entity\Keycompte;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Keycompte|null find($id, $lockMode = null, $lockVersion = null)
 * @method Keycompte|null findOneBy(array $criteria, array $orderBy = null)
 * @method Keycompte[]    findAll()
 * @method Keycompte[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class KeycompteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Keycompte::class);
    }

    // /**
    //  * @return Keycompte[] Returns an array of Keycompte objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('k')
            ->andWhere('k.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('k.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Keycompte
    {
        return $this->createQueryBuilder('k')
            ->andWhere('k.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
