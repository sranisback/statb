<?php
/**
 * Created by PhpStorm.
 * User: Sran_isback
 * Date: 08/03/2019
 * Time: 11:35
 */

namespace App\Service;

use App\Entity\MatchData;
use App\Entity\Matches;
use App\Entity\Players;
use Doctrine\ORM\EntityManagerInterface;

class MatchDataService
{

    private $doctrineEntityManager;

    public function __construct(EntityManagerInterface $doctrineEntityManager)
    {
        $this->doctrineEntityManager = $doctrineEntityManager;
    }

    public function creationLigneVideDonneeMatch(Players $player, Matches $match)
    {
        $matchdata = new MatchData();

        $matchdata->setAgg(0);
        $matchdata->setBh(0);
        $matchdata->setCp(0);
        $matchdata->setFMatch($match);
        $matchdata->setFPlayer($player);
        $matchdata->setInj(0);
        $matchdata->setIntcpt(0);
        $matchdata->setKi(0);
        $matchdata->setMvp(0);
        $matchdata->setSi(0);
        $matchdata->setTd(0);

        $this->doctrineEntityManager->persist($matchdata);

        $this->doctrineEntityManager->flush();
    }

    /**
     * @param MatchData $matchData
     * @return string
     */
    public function lectureLignedUnMatch(MatchData $matchData)
    {
        $ligneDuMatch = '';

            if ($matchData->getCp() > 0) {
                $ligneDuMatch .= 'CP: '.$matchData->getCp().', ';
            }

            if ($matchData->getTd() > 0) {
                $ligneDuMatch .= 'TD: '.$matchData->getTd().', ';
            }

            if ($matchData->getIntcpt() > 0) {
                $ligneDuMatch .= 'INT: '.$matchData->getIntcpt().',';
            }

            if (($matchData->getBh() + $matchData->getSi() + $matchData->getKi()) > 0) {
                $ligneDuMatch .= 'CAS: '.($matchData->getBh() + $matchData->getSi() + $matchData->getKi()).', ';
            }

            if ($matchData->getMvp() > 0) {
                $ligneDuMatch .= 'MVP: '.$matchData->getMvp().', ';
            }

            if ($matchData->getAgg() > 0) {
                $ligneDuMatch .= 'AGG: '.$matchData->getAgg().', ';
            }

        return $ligneDuMatch;
    }
}
