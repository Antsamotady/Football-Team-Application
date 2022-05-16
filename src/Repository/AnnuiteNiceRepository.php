<?php

namespace App\Repository;

use App\Entity\AnnuiteNice;
use App\Data\AnnuiteSearchData;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method AnnuiteNice|null find($id, $lockMode = null, $lockVersion = null)
 * @method AnnuiteNice|null findOneBy(array $criteria, array $orderBy = null)
 * @method AnnuiteNice[]    findAll()
 * @method AnnuiteNice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnnuiteNiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AnnuiteNice::class);
    }

    // /**
    //  * @return AnnuiteNice[] Returns an array of AnnuiteNice objects
    //  */
    public function findSearch(AnnuiteSearchData $search)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
                'SELECT p, c
                FROM App\Entity\Annuite p
                INNER JOIN p.name c
                WHERE p.name = :name'
            )->setParameter('name', "%{$search->getNom()}%");

        return $query->getOneOrNullResult();
    }

    /*
    public function findOneBySomeField($value): ?AnnuiteNice
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
