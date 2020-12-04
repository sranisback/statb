<?php

namespace App\Repository;

use App\Entity\Players;
use App\Entity\Teams;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Players|null find($id, $lockMode = null, $lockVersion = null)
 * @method Players|null findOneBy(array $criteria, array $orderBy = null)
 * @method Players[]    findAll()
 * @method Players[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlayersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
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
     * @return array
     */
    public function listeDesJoueursPourlEquipe(\App\Entity\Teams $equipe): array
    {
        return $this->getEntityManager()->getRepository(Players::class)->findBy(
            ['ownedByTeam' => $equipe->getTeamId()],
            ['nr' => 'ASC']
        );
    }

    /**
     * @param Teams $equipe
     * @return array
     */
    public function listeDesJoueursActifsPourlEquipe(Teams $equipe): array
    {
        return $this->createQueryBuilder('players')
            ->where('players.ownedByTeam = '.$equipe->getTeamId())
            ->andWhere('players.status != 7')
            ->andWhere('players.status != 8')
            ->andWhere('players.injRpm = 0 ')
            ->orderBy('players.nr')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Teams $equipe
     * @return mixed
     */
    public function listeDesJournaliersDeLequipe(Teams $equipe)
    {
        return $this->createQueryBuilder('players')
            ->where('players.ownedByTeam = '.$equipe->getTeamId())
            ->andWhere('players.status != 7')
            ->andWhere('players.type = 2')
            ->orderBy('players.nr', 'DESC')
            ->getQuery()
            ->getResult();
    }


    /**
     * @param int $year
     * @param int $limit
     * @return mixed
     */
    public function sousClassementEquipeFournisseurDeCadavre(int $year, int $limit = 0)
    {
        $query = $this->createQueryBuilder('players')
            ->select('teams.teamId, teams.name, COUNT(players) AS score')
            ->join('players.ownedByTeam', 'teams')
            ->where('players.status =8 AND players.journalier = FALSE AND teams.retired = 0 AND teams.year ='.$year)
            ->groupBy('teams.name')
            ->having('score > 0')
            ->addOrderBy('score', 'DESC')
            ->addOrderBy('teams.tv', 'DESC');

        if ($limit > 0) {
            $query->setMaxResults($limit);
        }

        return $query->getQuery()
            ->getResult();
    }
}
