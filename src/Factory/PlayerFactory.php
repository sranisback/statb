<?php

namespace App\Factory;

use App\Entity\Players;
use App\Entity\PlayersIcons;
use App\Entity\Teams;
use App\Enum\RulesetEnum;
use App\Tools\randomNameGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Utils\DateTime;

class PlayerFactory
{
    /**
     * @param $position
     * @param int $numero
     * @param Teams $equipe
     * @param bool $journalier
     * @param EntityManagerInterface $entityManager
     * @param string $ruleset
     * @param string|null $nom
     * @return Players
     */
    public static function nouveauJoueur(
        $position,
        int $numero,
        Teams $equipe,
        bool $journalier,
        EntityManagerInterface $entityManager,
        int $ruleset,
        string $nom = null
    ): Players {
        $joueur = new Players();
        $joueur->setRuleset($ruleset);

        $cost = $position->getCost();

        if (!empty($cost)) {
            $joueur->setValue($cost);
        }

        $dateBoughtFormat = DateTime::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s"));

        $joueur = RulesetEnum::setPositionAndRaceJoueurByRuleset($joueur,$position);

        $joueur = self::attribuerIcone($joueur, $entityManager);

        if ($dateBoughtFormat) {
            $joueur->setDateBought($dateBoughtFormat);
        }

        if (empty($nom)) {
            $generateurDeNom = new randomNameGenerator();
            $nom = $generateurDeNom->generateNames(1);
            $nom = $nom[0];
        }

        $joueur->setName($nom);
        $joueur->setOwnedByTeam($equipe);
        $joueur->setNr($numero);
        $joueur->setJournalier($journalier);
        $joueur->setStatus(1);

        return $joueur;
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @param $position
     * @return Players
     */
    private static function attribuerIcone(Players $joueur, EntityManagerInterface $entityManager): Players
    {
        $listeIcones = RulesetEnum::getIconeListeFromPlayerByRuleset($joueur, $entityManager);

        if ($listeIcones) {
            $joueur->setIcon($listeIcones[rand(0, count($listeIcones) - 1)]);
        } else {
            /** @var PlayersIcons $iconeNope */
            $iconeNope = $entityManager->getRepository(PlayersIcons::class)->findOneBy(['iconName' => 'nope']);

            $joueur->setIcon($iconeNope);
        }
        return $joueur;
    }
}
