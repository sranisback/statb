<?php

namespace App\Factory;

use App\Entity\Coaches;
use App\Entity\Races;
use App\Entity\RacesBb2020;
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
     * @param Races|RacesBb2020 $race
     * @param Coaches $coach
     * @param int $ruleset
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
        RulesetEnum::setFanFactorFromTeamByRuleset($equipe);

        $equipe->setOwnedByCoach($coach);

        return $equipe;
    }
}
