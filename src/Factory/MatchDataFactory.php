<?php
namespace App\Factory;

use App\Entity\MatchData;
use App\Entity\Matches;
use App\Entity\Players;

class MatchDataFactory
{
    /**
     * @param Players $joueur
     * @param Matches $match
     * @return MatchData
     */
    public static function ligneVide(Players $joueur, Matches $match): \App\Entity\MatchData
    {
        $matchdata = new MatchData();

        $matchdata->setAgg(0);
        $matchdata->setBh(0);
        $matchdata->setCp(0);
        $matchdata->setInj(0);
        $matchdata->setIntcpt(0);
        $matchdata->setKi(0);
        $matchdata->setMvp(0);
        $matchdata->setSi(0);
        $matchdata->setTd(0);
        $matchdata->setBonusSpp(0);
        $matchdata->setFMatch($match);
        $matchdata->setFPlayer($joueur);

        return $matchdata;
    }
}
