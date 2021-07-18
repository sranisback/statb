<?php

namespace App\Repository;

use App\Entity\MatchData;
use App\Entity\Matches;
use App\Entity\Players;
use App\Entity\Teams;
use App\Enum\RulesetEnum;
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
    public function sousClassementEquipe(int $year, string $type, int $limit = 0, int $ruleset)
    {
        $query = $this->createQueryBuilder('Matchdata')
            ->select('teams.teamId, teams.name ,race.icon')
            ->join('Matchdata.fPlayer', 'players')
            ->join('players.ownedByTeam', 'teams')
            ->where('teams.retired = 0 AND teams.year ='.$year)
            ->groupBy('teams.teamId')
            ->addOrderBy('score', 'DESC')
            ->addOrderBy('teams.tv', 'DESC')
            ->having('score > 0');

        switch ($ruleset) {
            case RulesetEnum::BB_2016:
                $query->join('players.fPos', 'game_data_players')
                    ->join('teams.fRace', 'race');
                break;
            case RulesetEnum::BB_2020:
                $query->join('players.fPosBb2020', 'game_data_players')
                    ->join('teams.race', 'race');
                break;
        }

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
    public function sousClassementJoueur(int $year, string $type, int $limit = 0, int $ruleset)
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
            ->join('players.icon', 'playersIcons')
            ->where('teams.retired = 0 AND teams.year ='.$year)
            ->groupBy('players.playerId')
            ->addOrderBy('score', 'DESC')
            ->addOrderBy('players.value', 'DESC')
            ->having('score > 0');

        switch ($ruleset) {
            case RulesetEnum::BB_2016:
                $query->join('players.fPos', 'game_data_players')
                    ->join('teams.fRace', 'race');
                break;
            case RulesetEnum::BB_2020:
                $query->join('players.fPosBb2020', 'game_data_players')
                    ->join('teams.race', 'race');
                break;
        }

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
                    (SUM(Matchdata.bh+Matchdata.si+Matchdata.ki)*2)+(SUM(Matchdata.mvp)*5)+ SUM(Matchdata.bonusSpp) AS score'
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
     * @return mixed|int
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function totalcas(int $year)
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

    /**
     * @param Matches $match
     * @param Teams $equipe
     * @return mixed
     */
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

    /**
     * @param Players $joueur
     * @return array|null
     */
    public function listeDesMatchsdUnJoueur(Players $joueur): ?array
    {
        $matchJoue = null;

        /** @var MatchData $dataMatches */
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
