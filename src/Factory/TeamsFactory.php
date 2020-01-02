<?php

namespace App\Factory;

use App\Entity\Coaches;
use App\Entity\Races;
use App\Entity\Stades;
use App\Entity\Teams;

class TeamsFactory
{
    /**
     * @param int $tresorDepart
     * @param string $nom
     * @param int $baseElo
     * @param Stades $stade
     * @param int $annee
     * @param Races $race
     * @param Coaches $coach
     * @return Teams
     */
    public function lancerEquipe(
        int $tresorDepart,
        string $nom,
        int $baseElo,
        Stades $stade,
        int $annee,
        Races $race,
        Coaches $coach
    ) {
        $equipe = new Teams();

        $equipe->setTreasury($tresorDepart);
        $equipe->setName($nom);
        $equipe->setElo($baseElo);
        $equipe->setTv(0);
        $equipe->setFStades($stade);
        $equipe->setYear($annee);
        $equipe->setFRace($race);
        $equipe->setOwnedByCoach($coach);

        return $equipe;
    }
}
