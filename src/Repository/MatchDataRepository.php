<?php

namespace App\Repository;

use App\Entity\MatchData;
use App\Entity\Matches;
use App\Entity\Teams;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

class MatchDataRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MatchData::class);
    }

    //TODO : deux methodes a revoir pour éviter la duplication de code ?

    /**
     * @param int $year
     * @param string $type
     * @param int $limit
     * @return mixed
     */
    public function sousClassementEquipe(int $year, string $type, int $limit = 0)
    {
        $query = $this->createQueryBuilder('Matchdata')
            ->select('teams.teamId, teams.name ,race.icon')
            ->join('Matchdata.fPlayer', 'players')
            ->join('players.ownedByTeam', 'teams')
            ->join('players.fPos', 'game_data_players')
            ->join('teams.fRace', 'race')
            ->where('teams.retired = 0 AND teams.year ='.$year)
            ->groupBy('teams.teamId')
            ->addOrderBy('score', 'DESC')
            ->addOrderBy('teams.tv', 'DESC')
            ->having('score > 0');

        switch ($type) {
            case 'bash':
                $query->addSelect('SUM(Matchdata.bh+Matchdata.si+Matchdata.ki) AS score');
                break;

            case 'td':
                $query->addSelect('SUM(Matchdata.td) AS score');
                break;

            case 'foul':
                $query->addSelect('SUM(Matchdata.agg) AS score');
                break;

            case 'killer':
                $query->addSelect('SUM(Matchdata.ki) AS score');
                break;
        }

        if ($limit > 0) {
            $query->setMaxResults($limit);
        }

        return $query->getQuery()->execute();
    }


    /**
     * @param int $year
     * @param string $type
     * @param int $limit
     * @return mixed
     */
    public function sousClassementJoueur(int $year, string $type, int $limit = 0)
    {
        $query = $this->createQueryBuilder('Matchdata')
            ->select(
                'players.nr, CASE WHEN players.name = \'\' THEN \'Inconnu\' ELSE players.name END AS name, 
                CASE WHEN players.status = 8 THEN  \'(Mort)\' ELSE \'\'  END AS dead, 
                CASE WHEN players.status = 7 THEN \'(Vendu)\' ELSE  \'\'  END AS sold, 
                teams.name AS equipe, teams.teamId AS equipeId, playersIcons.iconName AS icon '
            )
            ->join('Matchdata.fPlayer', 'players')
            ->join('players.ownedByTeam', 'teams')
            ->join('players.fPos', 'game_data_players')
            ->join('players.icon', 'playersIcons')
            ->join('teams.fRace', 'race')
            ->where('teams.retired = 0 AND teams.year ='.$year)
            ->groupBy('players.playerId')
            ->addOrderBy('score', 'DESC')
            ->addOrderBy('players.value', 'DESC')
            ->having('score > 0');

        switch ($type) {
            case 'bash':
                $query->addSelect('SUM(Matchdata.bh+Matchdata.si+Matchdata.ki) AS score');
                break;

            case 'td':
                $query->addSelect('SUM(Matchdata.td) AS score');
                break;

            case 'xp':
                $query->addSelect(
                    'SUM(Matchdata.cp) + (SUM(Matchdata.td)*3)+ (SUM(Matchdata.intcpt)*3)+ 
                    (SUM(Matchdata.bh+Matchdata.si+Matchdata.ki)*2)+(SUM(Matchdata.mvp)*5) AS score'
                );
                break;

            case 'pass':
                $query->addSelect('SUM(Matchdata.cp) AS score');
                break;

            case 'foul':
                $query->addSelect('SUM(Matchdata.agg) AS score');
                break;

            case 'killer':
                $query->addSelect('SUM(Matchdata.ki) AS score');
                break;

            case 'handi':
                $query->addSelect('SUM(Matchdata.si) AS score');
                break;
        }

        if ($limit > 0) {
            $query->setMaxResults($limit);
        }
        return $query->getQuery()->execute();
    }

    /**
     * @param int $year
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function totalcas($year)
    {
        $totalCas =  $this->createQueryBuilder('Matchdata')
            ->select('SUM(Matchdata.bh+Matchdata.si+Matchdata.ki) AS score')
            ->join('Matchdata.fPlayer', 'players')
            ->join('players.ownedByTeam', 'teams')
            ->where('teams.retired = 0 AND teams.year ='.$year)
            ->addOrderBy('score', 'DESC')
            ->having('score > 0')
            ->getQuery()
            ->getOneOrNullResult();

        if (!empty($totalCas)) {
            return $totalCas['score'];
        } else {
            return 0;
        }
    }

    public function listeDesJoueursdUnMatch(Matches $match, Teams $equipe)
    {
        return $this->createQueryBuilder('Matchdata')
            ->join('Matchdata.fMatch', 'Matches')
            ->join('Matchdata.fPlayer', 'Players')
            ->join('Players.ownedByTeam', 'Teams')
            ->where('Matches.matchId ='.$match->getMatchId())
            ->andWhere('Teams.teamId ='.$equipe->getTeamId())
            ->getQuery()
            ->execute();
    }

    public function listeDesMatchsdUnJoueur($joueur)
    {
        $matchJoue = null;

        foreach ($this->getEntityManager()->getRepository(MatchData::class)->findBy(
            ['fPlayer' => $joueur]
        ) as $dataMatches) {
            try {
                $matchJoue[] = $dataMatches->getFMatch();
            } catch (ORMException $e) {
            }
        }

        return $matchJoue;
    }
}
