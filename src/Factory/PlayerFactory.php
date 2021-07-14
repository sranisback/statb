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
    private const BB_2016 = '2016';

    private const BB_2020 = '2020';

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
        string $ruleset,
        string $nom = null
    ): Players {
        $joueur = new Players();

        $joueur = self::attribuerIcone($joueur,$entityManager, $position, $ruleset);

        $cost = $position->getCost();

        if (!empty($cost)) {
            $joueur->setValue($cost);
        }

        $dateBoughtFormat = DateTime::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s"));

        switch ($ruleset) {
            case self::BB_2016:
                $joueur = self::attributPositionEtRaceBb2016($position, $joueur);
                break;

            case self::BB_2020:
                $joueur = self::attributPositionEtRaceBb2020($position, $joueur);
                break;
        }


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

        $joueur->setRuleset(RulesetEnum::stringVersNumeroBdd()[$ruleset]);

        return $joueur;
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @param $position
     * @return Players
     */
    private static function attribuerIcone(Players $joueur,EntityManagerInterface $entityManager, $position, $ruleset): Players
    {
        switch ($ruleset) {
            case self::BB_2016:
                $listeIcones = $entityManager->getRepository(PlayersIcons::class)->toutesLesIconesDunePosition($position);
                break;

            case self::BB_2020:
                $listeIcones = $entityManager->getRepository(PlayersIcons::class)->toutesLesIconesDunePositionBb2020($position);
                break;
        }

        if ($listeIcones) {
            $joueur->setIcon($listeIcones[rand(0, count($listeIcones) - 1)]);
        } else {
            /** @var PlayersIcons $iconeNope */
            $iconeNope = $entityManager->getRepository(PlayersIcons::class)->findOneBy(['iconName' => 'nope']);

            $joueur->setIcon($iconeNope);
        }
        return $joueur;
    }

    /**
     * @param $position
     * @param Players $joueur
     */
    private static function attributPositionEtRaceBb2016($position, Players $joueur): Players
    {
        $race = $position->getFRace();

        if (!empty($race)) {
            $joueur->setFRid($race);
        }

        $joueur->setFPos($position);

        return $joueur;
    }

    /**
     * @param $position
     * @param Players $joueur
     * @return Players
     */
    private static function attributPositionEtRaceBb2020($position, Players $joueur): Players
    {
        $race = $position->getRace();

        if (!empty($race)) {
            $joueur->setFRidBb2020($race);
        }

        $joueur->setFPosBb2020($position);

        return $joueur;
    }
}
