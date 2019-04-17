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
}
