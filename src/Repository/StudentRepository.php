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
                ->andWhere('UPPER(u.name) LIKE UPPER(:name)')
                ->setParameter('name', "%{$search->getName()}%");
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
        $qb = $this
            ->createQueryBuilder('u')
            ->select('u');

        if (!empty($search->getName())) {
            $qb = $qb
                ->andWhere('UPPER(u.name) LIKE UPPER(:name)')
                ->setParameter('name', "%{$search->getName()}%");
        }

        if (!empty($search->getFanampiny())) {
            $qb = $qb
                ->andWhere('UPPER(u.fanampiny) LIKE UPPER(:fanampiny)')
                ->setParameter('fanampiny', "%{$search->getFanampiny()}%");
        }

        $result = $qb->getQuery()->getResult();
        
        return $result;

    }
}
