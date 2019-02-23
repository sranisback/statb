<?php

namespace App\Service;

use App\Entity\Coaches;
use App\Entity\Races;
use App\Entity\Setting;
use App\Entity\Teams;
use App\Entity\Matches;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;

class EquipeService
{

    private $doctrineEntityManager;

    private $tresorDepart = 1000000;

    private $baseElo = 150;

    public function __construct(EntityManagerInterface $doctrineEntityManager)
    {
        $this->doctrineEntityManager = $doctrineEntityManager;
    }

    /**
     * @param int $annee
     * @return array
     */
    public function toutesLesTeamsParAnnee($annee = 1)
    {
        if (!empty($this->doctrineEntityManager)) {
            return $this->doctrineEntityManager->getRepository(Teams::class)->findBy(
                ['year' => $annee, 'retired' => false],
                ['name' => 'ASC']
            );
        }

        return [];
    }

    /**
     * @param Teams $equipe
     * @return array
     */
    public function listeDesMatchs(Teams $equipe)
    {
        $matches1 = $this->doctrineEntityManager->getRepository(Matches::class)->findBy(
            ['team1' => $equipe->getTeamId()],
            ['dateCreated' => 'DESC']
        );

        $matches2 = $this->doctrineEntityManager->getRepository(Matches::class)->findBy(
            ['team2' => $equipe->getTeamId()],
            ['dateCreated' => 'DESC']
        );

        $matches = array_merge($matches1, $matches2);

        return $matches;
    }

    /**
     * @param Teams $equipe
     * @param array $matchesCollection
     * @return array
     */
    public function resultatsDelEquipe(Teams $equipe, Array $matchesCollection)
    {
        $TotalWin = 0;
        $Totaldraw = 0;
        $Totalloss = 0;

        foreach ($matchesCollection as $match) {
            $results = $this->resultatDuMatch($equipe, $match);

            $TotalWin += $results['win'];
            $Totaldraw += $results['draw'];
            $Totalloss += $results['loss'];
        }

        return ['win'=>$TotalWin,'draw'=> $Totaldraw,'loss'=> $Totalloss];
    }

    /**
     * @param Teams $equipe
     * @param Matches $match
     * @return array
     */
    public function resultatDuMatch(Teams $equipe, $match): array
    {
        $win = 0;
        $loss = 0;
        $draw = 0;

        if (($equipe === $match->getTeam1() && $match->getTeam1Score() > $match->getTeam2Score(
        )) || ($equipe === $match->getTeam2() && $match->getTeam1Score() < $match->getTeam2Score())) {
            $win++;
        } elseif (($equipe === $match->getTeam1() && $match->getTeam1Score() < $match->getTeam2Score(
        )) || ($equipe === $match->getTeam2() && $match->getTeam1Score() > $match->getTeam2Score())) {
            $loss++;
        } elseif (($equipe === $match->getTeam1() && $match->getTeam1Score() == $match->getTeam2Score(
        )) || ($equipe === $match->getTeam2() && $match->getTeam1Score() == $match->getTeam2Score())) {
            $draw++;
        }

        return ['win'=>$win, 'loss'=>$loss,'draw'=> $draw];
    }

    /**
     * @param string $teamname
     * @param int $coachid
     * @param int $raceid
     * @return int|null
     */
    public function createTeam($teamname, $coachid, $raceid)
    {
        $setting = $this->doctrineEntityManager->getRepository(Setting::class)->findOneBy(['name' => 'year']);
        $race = $this->doctrineEntityManager->getRepository(Races::class)->findOneBy(['raceId' => $raceid]);
        $coach = $this->doctrineEntityManager->getRepository(Coaches::class)->findOneBy(array('coachId' => $coachid));

        $currentYear = 0;
        $teamid = 0;
        $team = new Teams();

        $team->setTreasury($this->tresorDepart);
        $team->setName($teamname);
        $team->setElo($this->baseElo);
        if ($setting) {
            try {
                $currentYear = $setting->getValue();
            } catch (ORMException $e) {
            }

            $team->setYear((int)$currentYear);
        }
        if ($race) {
            $team->setFRace($race);
        }
        if ($coach) {
            $team->setOwnedByCoach($coach);
        }

        try {
            $this->doctrineEntityManager->persist($team);
            $this->doctrineEntityManager->flush();
            $this->doctrineEntityManager->refresh($team);
            $teamid = $team->getTeamId();
        } catch (ORMException $e) {
        }

        return $teamid;
    }
}
