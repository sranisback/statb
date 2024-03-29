<?php

namespace App\Factory;

use App\Entity\GameDataStadium;
use App\Entity\Matches;
use App\Entity\Meteo;
use App\Entity\Teams;
use Nette\Utils\DateTime;

class MatchesFactory
{
    /**
     * @param array<string,mixed> $donnees
     * @param Teams $equipe1
     * @param Teams $equipe2
     * @param int $tv1
     * @param int $tv2
     * @param Meteo $meteo
     * @param GameDataStadium $stade
     * @return Matches
     */
    public static function creerUnMatch(
        Array $donnees,
        Teams $equipe1,
        Teams $equipe2,
        int $tv1,
        int $tv2,
        Meteo $meteo,
        GameDataStadium $stade
    ): Matches {
        $dateMatch = DateTime::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s"));

        $match = new Matches();

        if (!empty($dateMatch)) {
            $match->setFans($donnees['totalpop']);
            $match->setFfactor1($donnees['varpop_team1']);
            $match->setFfactor2($donnees['varpop_team2']);
            $match->setIncome1($donnees['gain1']);
            $match->setIncome2($donnees['gain2']);
            $match->setTeam1Score($donnees['score1']);
            $match->setTeam2Score($donnees['score2']);
            $match->setTeam1($equipe1);
            $match->setTeam2($equipe2);
            $match->setTv1($tv1);
            $match->setTv2($tv2);
            $match->setFMeteo($meteo);
            $match->setFStade($stade);
            $match->setDateCreated($dateMatch);
            $match->setStadeAcceuil($donnees['stadeAccueil']);
            if (!empty($donnees['depense1'])) {
                $match->setDepense1((int) -$donnees['depense1']);
            }
            if (!empty($donnees['depense2'])) {
                $match->setDepense2((int) -$donnees['depense2']);
            }
            $match->setScoreClassementTeam1($equipe1->getScore());
            $match->setScoreClassementTeam2($equipe2->getScore());
        }

        return $match;
    }
}
