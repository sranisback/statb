<?php

namespace App\Service;

use App\Entity\Teams;
use App\Entity\Matches;

use Doctrine\Common\Persistence\ManagerRegistry;

class EquipeService
{

    private $doctrineEntityManager;

    public function __construct(ManagerRegistry $doctrineEntityManager)
    {
        $this->doctrineEntityManager = $doctrineEntityManager;
    }

    /**
     * @param int $annee
     * @return array
     */
    public function toutesLesTeamsParAnnee($annee = 1)
    {
        return $this->doctrineEntityManager->getRepository(Teams::class)->findBy(
            ['year' => $annee, 'retired' => false],
            ['name' => 'ASC']
        );
    }

    /**
     * @param Teams $equipe
     * @return array
     */
    public function listeDesMatchs(Teams $equipe)
    {

        $matches1 = $this->doctrineEntityManager->getRepository(Matches::class)->findBy(
            array('team1' => $equipe->getTeamId()),
            array('dateCreated' => 'DESC')
        );
        $matches2 = $this->doctrineEntityManager->getRepository(Matches::class)->findBy(
            array('team2' => $equipe->getTeamId()),
            array('dateCreated' => 'DESC')
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
}
