<?php

namespace App\Repository;

use App\Entity\Penalite;
use App\Entity\Teams;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Penalite|null find($id, $lockMode = null, $lockVersion = null)
 * @method Penalite|null findOneBy(array $criteria, array $orderBy = null)
 * @method Penalite[]    findAll()
 * @method Penalite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PenaliteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Penalite::class);
    }

    public function listePenaliteEnCours(int $annee)
    {
        return $this->createQueryBuilder('penalite')
            ->join('penalite.equipe', 'teams')
            ->where('teams.year = ' . $annee)
            ->getQuery()
            ->getResult();
    }

    public function penaliteDuneEquipe(Teams $equipe)
    {
        return $this->createQueryBuilder('penalite')
            ->select('SUM(penalite.points) malus')
            ->where('penalite.equipe = ' . $equipe->getTeamId())
            ->getQuery()->getSingleScalarResult();
    }
}
