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

        $this->applyBasicFilters($qb, $search);

        $this->applyScoreFilters($qb, $search);

        $qb->orderBy('u.firstname', 'ASC');

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
        $qb = $this
            ->createQueryBuilder('u')
            ->select('u')
            ->distinct();

        $this->applyBasicFilters($qb, $search);

        $this->applyClasseAndLocationFilters($qb, $search);

        $this->applyScoreFilters($qb, $search);

        // Optional: Order the results
        $qb->orderBy('u.firstname', 'ASC');

        // For debugging:
        // dump($qb->getQuery()->getSQL());
        // dump($qb->getQuery()->getParameters());

        /** @var Student[] $result */
        $result = $qb->getQuery()->getResult();
        
        return $result;
    }

    /**
     * Student linked to search
     *
     * @return Student[]
     */
    public function findByClasseOrderedByFirstName(Classe $classe): array
    {
        return $this->findBy(
            ['classe' => $classe], 
            ['firstname' => 'ASC']
        );
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

    private function applyBasicFilters(QueryBuilder $qb, StudentFilterData $search): void 
    {
        if (!empty($search->getFirstname())) {
            $qb->andWhere('UPPER(u.firstname) LIKE UPPER(:firstname)')
                ->setParameter('firstname', "%{$search->getFirstname()}%");
        }

        if (!empty($search->getLastname())) {
            $qb->andWhere('UPPER(u.lastname) LIKE UPPER(:lastname)')
                ->setParameter('lastname', "%{$search->getLastname()}%");
        }

        if (!empty($search->getGender())) {
            $qb->andWhere('u.gender = :gender')
                ->setParameter('gender', $search->getGender());
        }

        if (!empty($search->getStudentNumber())) {
            $qb->andWhere('u.studentNumber = :studentNumber')
                ->setParameter('studentNumber', $search->getStudentNumber());
        }
    }

    private function applyClasseAndLocationFilters(QueryBuilder $qb, StudentFilterData $search): void 
    {
        $classe = $search->getClasse();
        $location = $search->getLocation();

        if ($classe === null && $location === null) {
            return;
        }

        $qb->leftJoin('u.classe', 'c');

        if ($classe !== null) {
            $qb->andWhere('c.id = :classeId')
            ->setParameter('classeId', $classe->getId());
        }

        if ($location !== null) {
            $qb->leftJoin('c.location', 'l')
            ->andWhere('l.id = :locationId')
            ->setParameter('locationId', $location->getId());
        }
    }

    private function applyScoreFilters(QueryBuilder $qb, StudentFilterData $search): void 
    {
        $scoreFilters = $search->getScoreFilters();
        if ($scoreFilters->count() > 0) {
            foreach ($scoreFilters as $index => $filter) {
                $hasMin = $filter->getMin() !== null;
                $hasMax = $filter->getMax() !== null;
                $hasSubject = $filter->getSubject() !== null;
                
                if ($hasMin || $hasMax || $hasSubject) {
                    
                    if ($hasSubject) {
                        // CASE 1: With subject
                        $joinAlias = "sc_$index";
                        $qb->join('u.scores', $joinAlias)
                        ->andWhere("$joinAlias.subject = :subject_$index")
                        ->setParameter("subject_$index", $filter->getSubject());
                        
                        if ($hasMin) {
                            $qb->andWhere("$joinAlias.value >= :min_$index")
                            ->setParameter("min_$index", $filter->getMin());
                        }
                        
                        if ($hasMax) {
                            $qb->andWhere("$joinAlias.value <= :max_$index")
                            ->setParameter("max_$index", $filter->getMax());
                        }
                        
                    } else if ($hasMin || $hasMax) {
                        // CASE 2: Global min/max without subject
                        // Join scores and check all are within range
                        $joinAlias = "sc_all_$index";
                        $qb->leftJoin('u.scores', $joinAlias);
                        
                        // Group by student to use HAVING clause
                        $qb->addGroupBy('u.id');
                        
                        $conditions = [];
                        
                        if ($hasMin) {
                            // MIN condition: no score should be below min
                            $conditions[] = "MIN($joinAlias.value) >= :global_min_$index";
                            $qb->setParameter("global_min_$index", $filter->getMin());
                        }
                        
                        if ($hasMax) {
                            // MAX condition: no score should be above max
                            $conditions[] = "MAX($joinAlias.value) <= :global_max_$index";
                            $qb->setParameter("global_max_$index", $filter->getMax());
                        }
                        
                        $qb->having(implode(' AND ', $conditions));
                    }
                }
            }
        }
    }
}
