<?php

namespace App\Repository;

use App\Entity\GameDataStadium;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GameDataStadium|null find($id, $lockMode = null, $lockVersion = null)
 * @method GameDataStadium|null findOneBy(array $criteria, array $orderBy = null)
 * @method GameDataStadium[]    findAll()
 * @method GameDataStadium[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameDataStadiumRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GameDataStadium::class);
    }
}
