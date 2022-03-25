<?php

namespace App\Repository;

use App\Entity\Abonnement;
use App\Data\ClientFilterData;
use App\Data\ClientSearchData;
use App\Form\AbonnementFormType;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Abonnement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Abonnement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Abonnement[]    findAll()
 * @method Abonnement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AbonnementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Abonnement::class);
    }

    /**
     * Client linked to search
     *
     * @return Abonnement[]
     */
    public function findSearch(ClientSearchData $search): array
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
    * @return Abonnement[]
    */
    public function findFiltered(ClientFilterData $search): array
    {
        $qb = $this
            ->createQueryBuilder('u')
            ->select('u');

        if (!empty($search->getName()))
        {
            $qb = $qb
                ->andWhere('UPPER(u.nomClient) LIKE UPPER(:nom)')        // Doctrine LIKE case insensitive
                ->setParameter('nom', "%{$search->getName()}%");
        }

        if (!empty($search->getNbTitre()))
        {
            $qb = $qb
                ->andWhere('u.nbTitres = :nbT')        // Doctrine LIKE case insensitive
                ->setParameter('nbT', $search->getNbTitre());
        }

        if ( !is_null($search->getFlagActif()) && $search->getFlagActif() == 1 )
        {
            $qb = $qb
                ->andWhere('u.flagActif = TRUE');        // Doctrine LIKE case insensitive
        }

        if ( !is_null($search->getFlagActif()) && $search->getFlagActif() == 0 )
        {
            $qb = $qb
                ->andWhere('u.flagActif = FALSE');        // Doctrine LIKE case insensitive
        }

        // 0 1
        if ( empty($search->getDateMin()) && !empty($search->getDateMax()) ) {
            $qb = $qb
                ->andWhere('u.dateFin <= :Dmax')
                ->setParameter('Dmax', $search->getDateMax());
        }
        // 1 0
        if ( !empty($search->getDateMin()) && empty($search->getDateMax()) ) {
            $qb = $qb
                ->andWhere('u.dateFin >= :Dmin')
                ->setParameter('Dmin', $search->getDateMin());
        }
        // 1 1
        if ( !empty($search->getDateMin()) && !empty($search->getDateMax()) ) {
            $qb = $qb
                ->andWhere('u.dateFin >= :Dmin AND u.dateFin <= :Dmax')
                ->setParameter('Dmin', $search->getDateMin())
                ->setParameter('Dmax', $search->getDateMax());
        }


        $result = $qb->getQuery()->getResult();
        
        return $result;

    }

    // /**
    //  * @return Abonnement[] Returns an array of Abonnement objects
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
    public function findOneBySomeField($value): ?Abonnement
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
