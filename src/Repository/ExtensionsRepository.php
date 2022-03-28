<?php

namespace App\Repository;

use App\Entity\Extensions;
use App\Data\ExtensionsFilterData;
use App\Data\ExtensionsSearchData;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Extensions|null find($id, $lockMode = null, $lockVersion = null)
 * @method Extensions|null findOneBy(array $criteria, array $orderBy = null)
 * @method Extensions[]    findAll()
 * @method Extensions[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExtensionsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Extensions::class);
    }

    /**
     * Client linked to search
     *
     * @return Extensions[]
     */
    public function findSearch(ExtensionsSearchData $search): array
    {
        $qb = $this
            ->createQueryBuilder('u')
            ->select('u');

        if (!empty($search->getNom()))
        {
            $qb = $qb
                ->andWhere('UPPER(u.nomClient) LIKE UPPER(:nom)')        // Doctrine LIKE case insensitive
                ->setParameter('nom', "%{$search->getNom()}%");
        }

        $result = $qb->getQuery()->getResult();

        return $result;

    }

    /**
    * User linked to search
    *
    * @return Extensions[]
    */
    public function findFiltered(ExtensionsFilterData $search): array
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
                ->andWhere('UPPER(u.periode) LIKE UPPER(:periode)')        // Doctrine LIKE case insensitive
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
    //  * @return Extensions[] Returns an array of Extensions objects
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
    public function findOneBySomeField($value): ?Extensions
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
