<?php

namespace App\Repository;

use App\Entity\Classe;
use App\Entity\Student;
use Doctrine\ORM\QueryBuilder;
use App\Data\StudentFilterData;
use App\Data\StudentSearchData;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Student>
 */
class StudentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Student::class);
    }

    /**
     * Student linked to search
     *
     * @return Student[]
     */
    public function findSearch(StudentSearchData $search): array
    {
        $qb = $this
            ->createQueryBuilder('u')
            ->select('u');

        if (!empty($search->getName()))
        {
            $qb = $qb
                ->andWhere('UPPER(u.firstname) LIKE UPPER(:firstname)')
                ->orWhere('UPPER(u.lastname) LIKE UPPER(:firstname)')
                ->setParameter('firstname', "%{$search->getName()}%");
        }

        /** @var Student[] $result */
        $result = $qb->getQuery()->getResult();

        return $result;
    }

    /**
    * User linked to search
    *
    * @return Student[]
    */
    public function findFilteredByClasse(Classe $classe, StudentFilterData $search): array
    {
        $qb = $this
            ->createQueryBuilder('u')
            ->select('u')
            ->join('u.classe', 'c')
            ->where('c.id = :searchedObjId')
            ->setParameter('searchedObjId', $classe->getId());

        if (!empty($search->getFirstname())) {
            $qb = $qb
                ->andWhere('UPPER(u.firstname) LIKE UPPER(:firstname)')
                ->setParameter('firstname', "%{$search->getFirstname()}%");
        }

        if (!empty($search->getLastname())) {
            $qb = $qb
                ->andWhere('UPPER(u.lastname) LIKE UPPER(:lastname)')
                ->setParameter('lastname', "%{$search->getLastname()}%");
        }

        if (!empty($search->getGender())) {
            $qb = $qb
                ->andWhere('UPPER(u.gender) LIKE UPPER(:gender)')
                ->setParameter('gender', "{$search->getGender()}");
        }

        // dump($qb->getQuery()->getSQL());

        /** @var Student[] $result */
        $result = $qb->getQuery()->getResult();
        
        return $result;
    }

    /**
    * User linked to search
    *
    * @return Student[]
    */
    public function findFiltered(StudentFilterData $search): array
    {
        $em = $this->getEntityManager();

        $qb = $this
            ->createQueryBuilder('u')
            ->select('u');

        if (!empty($search->getFirstname())) {
            $qb = $qb
                ->andWhere('UPPER(u.firstname) LIKE UPPER(:firstname)')
                ->setParameter('firstname', "%{$search->getFirstname()}%");
        }

        if (!empty($search->getLastname())) {
            $qb = $qb
                ->andWhere('UPPER(u.lastname) LIKE UPPER(:lastname)')
                ->setParameter('lastname', "%{$search->getLastname()}%");
        }

        if (!empty($search->getGender())) {
            $qb = $qb
                ->andWhere('UPPER(u.gender) LIKE UPPER(:gender)')
                ->setParameter('gender', "{$search->getGender()}");
        }

        if (!empty($search->getClasse())) {
            $qb = $qb
                ->join('u.classe', 'c')
                ->andWhere('c.name LIKE :searchedString')
                ->setParameter('searchedString', $search->getClasse()->getName());
        }

        if (!empty($search->getClasse()) || !empty($search->getLocation())) {
            $qb->join('u.classe', 'c2'); // Classe join

            if (!empty($search->getClasse())) {
                $qb->andWhere('c2.id = :classeId')
                ->setParameter('classeId', $search->getClasse()->getId());
            }

            if (!empty($search->getLocation())) {
                $qb->join('c2.location', 'l'); // join Location
                $qb->andWhere('l.id = :locationId')
                ->setParameter('locationId', $search->getLocation()->getId());
            }
        }

        // dump($qb->getQuery()->getSQL());

        /** @var Student[] $result */
        $result = $qb->getQuery()->getResult();
        
        return $result;
    }

    /**
     * Student linked to search
     *
     * @return Student[]
     */
    public function findAllOrderedByFirstName(): array
    {
        return $this->findBy([], ['firstname' => 'ASC']);
    }
}
