<?php

namespace App\Repository;

use App\Entity\AnnuiteLocarno;
use App\Data\AnnuiteSearchData;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

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

    /**
     * @return AnnuiteLocarno[] Returns an array of AnnuiteLocarno objects
     */
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
