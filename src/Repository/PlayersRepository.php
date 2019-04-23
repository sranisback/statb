<?php

namespace App\Repository;

use App\Entity\Players;
use App\Entity\Teams;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Players|null find($id, $lockMode = null, $lockVersion = null)
 * @method Players|null findOneBy(array $criteria, array $orderBy = null)
 * @method Players[]    findAll()
 * @method Players[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlayersRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Players::class);
    }

    public function mortPourlAnnee($annee)
    {
        return $this->createQueryBuilder('players')
            ->join('players.ownedByTeam', 'teams')
            ->where('teams.year = '.$annee)
            ->andWhere('players.status = 8')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Teams $equipe
     */
    public function listeDesJoueursPourlEquipe($equipe)
    {
        return $this->getEntityManager()->getRepository(Players::class)->findBy(
            ['ownedByTeam' => $equipe->getTeamId()],
            ['nr' => 'ASC'] );
    }

    /**
     * @param Teams $equipe
     */
    public function listeDesJoueursActifsPourlEquipe($equipe)
    {
        return $this->createQueryBuilder('players')
            ->where('players.ownedByTeam = '.$equipe->getTeamId())
            ->andWhere('players.status != 7 AND players.status != 9')
            ->orderBy('players.nr')
            ->getQuery()
            ->getResult();
    }
}
