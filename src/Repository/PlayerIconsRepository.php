<?php

namespace App\Repository;

use App\Entity\GameDataPlayers;
use App\Entity\PlayersIcons;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PlayersIcons|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlayersIcons|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlayersIcons[]    findAll()
 * @method PlayersIcons[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlayerIconsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlayersIcons::class);
    }

    public function toutesLesIconesDunePosition(GameDataPlayers $position)
    {
        return $this->findBy(['position' => $position]);
    }
}
