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

        $joueur = new Players();

        $joueur->setNr($numero);
        $joueur->setFCid($equipe->getOwnedByCoach());
        $joueur->setFRid($position->getFRace());

        if ($dateBoughtFormat) {
            $joueur->setDateBought($dateBoughtFormat);
        }

        $joueur->setFPos($position);
        $joueur->setOwnedByTeam($equipe);
        $joueur->setValue($position->getCost());
        $joueur->setType($type);
        $joueur->setStatus(1);

        return $joueur;
    }
}