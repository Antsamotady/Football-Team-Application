<?php

namespace App\Repository;

use App\Entity\Player;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Player|null find($id, $lockMode = null, $lockVersion = null)
 * @method Player|null findOneBy(array $criteria, array $orderBy = null)
 * @method Player[]    findAll()
 * @method Player[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlayerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Player::class);
    }

    public function findPlayersAvailableForSale(int $teamId): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.team != :teamId')
            ->orWhere('p.team IS NULL')
            ->orWhere('p.isAvailableForSale = true')
            ->setParameter('teamId', $teamId)
            ->orderBy('p.name')
            ->getQuery()
            ->getResult();
    }

    public function findBySearchData($player = null)
    {
        $queryBuilder = $this->createQueryBuilder('p');

        if (null !== $player) {
            if ($player->getName()) {
                $name = strtolower($player->getName());

                $queryBuilder->andWhere('LOWER(p.name) LIKE :name')
                    ->setParameter('name', '%' . $name . '%');
            }
    
            // Add more conditions for other search fields as needed
    
        }
    
        $query = $queryBuilder->getQuery();
        
        return $query->getResult();
    }
}
