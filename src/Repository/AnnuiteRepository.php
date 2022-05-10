<?php

namespace App\Repository;

use App\Entity\Annuite;
use App\Data\AnnuiteFilterData;
use App\Data\AnnuiteSearchData;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Annuite|null find($id, $lockMode = null, $lockVersion = null)
 * @method Annuite|null findOneBy(array $criteria, array $orderBy = null)
 * @method Annuite[]    findAll()
 * @method Annuite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnnuiteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Annuite::class);
    }

    /**
     * Client linked to search
     *
     * @return Annuite[]
     */
    public function findSearch(AnnuiteSearchData $search): array
    {
        $qb = $this
            ->createQueryBuilder('u')
            ->select('u');

        if (!empty($search->getNom()))
        {
            $qb = $qb
                ->andWhere('UPPER(u.pays) LIKE UPPER(:pays)')        // Doctrine LIKE case insensitive
                ->setParameter('pays', "%{$search->getNom()}%");
        }

        $result = $qb->getQuery()->getResult();

        return $result;

    }

    /**
    * Annuite linked to search
    *
    * @return Annuite[]
    */
    public function findFiltered(AnnuiteFilterData $search): array
    {
        $qb = $this
            ->createQueryBuilder('u')
            ->select('u');

        if (!empty($search->getPays()))
        {
            $qb = $qb
                ->andWhere('UPPER(u.pays) LIKE UPPER(:pays)')        // Doctrine LIKE case insensitive
                ->setParameter('pays', "%{$search->getPays()}%");
        }

        if (!empty($search->getPeriode()))
        {
            $qb = $qb
                ->andWhere('UPPER(u.periode) LIKE UPPER(:periode)')
                ->setParameter('periode', "%{$search->getPeriode()}%");
        }

        if (!empty($search->getMontant()))
        {
            $qb = $qb
                ->andWhere('UPPER(u.montants) LIKE UPPER(:montants)')        // Doctrine LIKE case insensitive
                ->setParameter('montants', "%{$search->getMontant()}%");
        }

        if (!empty($search->getRegion()))
        {
            $qb = $qb
                ->andWhere('UPPER(u.region) LIKE UPPER(:region)')        // Doctrine LIKE case insensitive
                ->setParameter('region', "%{$search->getRegion()}%");
        }


        $result = $qb->getQuery()->getResult();
        
        return $result;

    }

    // /**
    //  * @return Annuite[] Returns an array of Annuite objects
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
    public function findOneBySomeField($value): ?Annuite
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
