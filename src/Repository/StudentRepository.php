<?php

namespace App\Repository;

use App\Entity\Student;
use App\Data\StudentFilterData;
use App\Data\StudentSearchData;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Student|null find($id, $lockMode = null, $lockVersion = null)
 * @method Student|null findOneBy(array $criteria, array $orderBy = null)
 * @method Student[]    findAll()
 * @method Student[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
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

        // dump($qb->getQuery()->getSQL());

        $result = $qb->getQuery()->getResult();
        
        return $result;

    }
}
