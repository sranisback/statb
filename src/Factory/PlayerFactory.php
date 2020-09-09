<?php

namespace App\Factory;

use App\Entity\GameDataPlayers;
use App\Entity\Players;
use App\Entity\PlayersIcons;
use App\Entity\Teams;
use App\Tools\randomNameGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Utils\DateTime;

class PlayerFactory
{
    /**
     * @param GameDataPlayers $position
     * @param int $numero
     * @param Teams $equipe
     * @param int $type
     * @param string|null $nom
     * @param EntityManagerInterface $entityManager
     * @return Players
     */
    public function nouveauJoueur(
        GameDataPlayers $position,
        int $numero,
        Teams $equipe,
        int $type,
        string $nom = null,
        \Doctrine\ORM\EntityManagerInterface $entityManager
    ): \App\Entity\Players {
        $dateBoughtFormat = DateTime::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s"));

        $race = $position->getFRace();
        $cost = $position->getCost();

        $listeIcones = $entityManager->getRepository(PlayersIcons::class)->toutesLesIconesDunePosition($position);

        $joueur = new Players();

        if ($listeIcones) {
            $joueur->setIcon($listeIcones[ rand(0, count($listeIcones) - 1)]);
        } else {
            /** @var PlayersIcons $iconeNope */
            $iconeNope = $entityManager->getRepository(PlayersIcons::class)->findOneBy(['iconName' => 'nope']);

            $joueur->setIcon($iconeNope);
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

        if (empty($nom)) {
            $generateurDeNom = new randomNameGenerator();
            $nom = $generateurDeNom->generateNames(1);
            $nom = $nom[0];
        }

        $joueur->setName($nom);
        $joueur->setFPos($position);
        $joueur->setOwnedByTeam($equipe);
        $joueur->setNr($numero);
        $joueur->setType($type);
        $joueur->setStatus(1);

        return $joueur;
    }
}
