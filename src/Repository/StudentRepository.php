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
    public function findFilteredOld(StudentFilterData $search): array
    {
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

        // Score filters
        $scoreFilters = $search->getScoreFilters();

        if ($scoreFilters->count() > 0) {
            // Join scores table
            $qb->leftJoin('u.scores', 'sc');
            
            // Group score conditions with OR if you want ANY score to match
            // Use AND if you want ALL conditions to match
            $scoreConditions = [];
            
            foreach ($scoreFilters as $index => $filter) {
                $hasMin = $filter->getMin() !== null;
                $hasMax = $filter->getMax() !== null;
                
                if ($hasMin || $hasMax) {
                    $condition = '';
                    
                    if ($hasMin) {
                        $condition .= "sc.value >= :min_$index";
                        $qb->setParameter("min_$index", $filter->getMin());
                    }
                    
                    if ($hasMin && $hasMax) {
                        $condition .= ' AND ';
                    }
                    
                    if ($hasMax) {
                        $condition .= "sc.value <= :max_$index";
                        $qb->setParameter("max_$index", $filter->getMax());
                    }
                    
                    $scoreConditions[] = "($condition)";
                }
            }
            
            // If we have score conditions, add them to the query
            if (!empty($scoreConditions)) {
                // Use OR to match any of the score filters
                // Change to AND if you want to match all filters
                $qb->andWhere(implode(' OR ', $scoreConditions));
            }
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
        $qb = $this
            ->createQueryBuilder('u')
            ->select('u')
            ->distinct();

        // Basic filters (firstname, lastname, gender)
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

        // Classe and Location filters
        $classe = $search->getClasse();
        $location = $search->getLocation();

        if ($classe !== null || $location !== null) {
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


        $scoreFilters = $search->getScoreFilters();
        if ($scoreFilters->count() > 0) {
            foreach ($scoreFilters as $index => $filter) {
                $hasMin = $filter->getMin() !== null;
                $hasMax = $filter->getMax() !== null;
                $hasSubject = $filter->getSubject() !== null;
                
                // Only create join if we have conditions
                if ($hasMin || $hasMax || $hasSubject) {
                    $joinAlias = "sc_$index";
                    $qb->join('u.scores', $joinAlias);
                    
                    if ($hasMin) {
                        $qb->andWhere("$joinAlias.value >= :min_$index")
                        ->setParameter("min_$index", $filter->getMin());
                    }
                    
                    if ($hasMax) {
                        $qb->andWhere("$joinAlias.value <= :max_$index")
                        ->setParameter("max_$index", $filter->getMax());
                    }
                    
                    if ($hasSubject) {
                        $qb->andWhere("$joinAlias.subject = :subject_$index")
                        ->setParameter("subject_$index", $filter->getSubject());
                    }
                }
            }
        }

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
    public function findAllOrderedByFirstName(): array
    {
        return $this->findBy([], ['firstname' => 'ASC']);
    }
}
