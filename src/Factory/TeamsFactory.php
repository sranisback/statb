<?php

namespace App\Factory;

use App\Entity\Coaches;
use App\Entity\Stades;
use App\Entity\Teams;
use App\Enum\RulesetEnum;

class TeamsFactory
{
    /**
     * @param int $tresorDepart
     * @param string $nom
     * @param int $baseElo
     * @param Stades $stade
     * @param int $annee
     * @param $race
     * @param Coaches $coach
     * @return Teams
     */
    public static function lancerEquipe(
        int $tresorDepart,
        string $nom,
        int $baseElo,
        Stades $stade,
        int $annee,
        $race,
        Coaches $coach,
        int $ruleset
    ): Teams {
        $equipe = new Teams();

        $equipe->setTreasury($tresorDepart);
        $equipe->setName($nom);
        $equipe->setElo($baseElo);
        $equipe->setTv(0);
        $equipe->setFStades($stade);
        $equipe->setYear($annee);
        $equipe->setRuleset($ruleset);

        RulesetEnum::setTeamRaceFromTeamByRuleset($equipe,$race);

        $equipe->setOwnedByCoach($coach);

        return $equipe;
    }
}
