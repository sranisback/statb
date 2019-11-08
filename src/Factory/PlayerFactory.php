<?php

namespace App\Factory;

use App\Entity\GameDataPlayers;
use App\Entity\Players;
use App\Entity\Teams;
use Nette\Utils\DateTime;

class PlayerFactory
{
    /**
     * @param GameDataPlayers $position
     * @param int $numero
     * @param Teams $equipe
     * @param int $type
     * @return Players
     */
    public function nouveauJoueur(GameDataPlayers $position, int $numero, Teams $equipe, int $type)
    {
        $dateBoughtFormat = DateTime::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s"));

        $coach = $equipe->getOwnedByCoach();
        $race = $position->getFRace();
        $cost = $position->getCost();

        $joueur = new Players();

        if (!empty($coach)) {
            $joueur->setFCid($coach);
        }
        if (!empty($race)) {
            $joueur->setFRid($race);
        }

        if ($dateBoughtFormat) {
            $joueur->setDateBought($dateBoughtFormat);
        }

        if (!empty($cost)) {
            $joueur->setValue($cost);
        }

        $joueur->setFPos($position);
        $joueur->setOwnedByTeam($equipe);
        $joueur->setNr($numero);
        $joueur->setType($type);
        $joueur->setStatus(1);

        return $joueur;
    }
}
