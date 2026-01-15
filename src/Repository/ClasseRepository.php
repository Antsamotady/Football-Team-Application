<?php

namespace App\Repository;

use App\Entity\Classe;
use App\Data\GeneralSearchData;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Classe|null find($id, $lockMode = null, $lockVersion = null)
 * @method Classe|null findOneBy(array $criteria, array $orderBy = null)
 * @method Classe[]    findAll()
 * @method Classe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
/**
 * @extends ServiceEntityRepository<Classe>
 */
class ClasseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Classe::class);
    }

    /**
     * Classe linked to search
     *
     * @return Classe[]
     */
    public function findSearch(GeneralSearchData $search): array
    {
        $qb = $this->createQueryBuilder('u')
            ->leftJoin('u.location', 'l')
            ->addSelect('l');

        if (!empty($search->getName())) {
            $qb
                ->andWhere(
                    'UPPER(u.name) LIKE UPPER(:term)
                    OR UPPER(l.name) LIKE UPPER(:term)'
                )
                ->setParameter('term', '%' . $search->getName() . '%');
        }

        /** @var Classe[] $result */
        $result = $qb->getQuery()->getResult();
        
        return $result;
    }

}
