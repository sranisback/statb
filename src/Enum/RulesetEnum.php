<?php


namespace App\Enum;


use App\Entity\GameDataPlayers;
use App\Entity\GameDataPlayersBb2020;
use App\Entity\GameDataSkills;
use App\Entity\GameDataSkillsBb2020;
use App\Entity\Players;
use App\Entity\PlayersIcons;
use App\Entity\Teams;
use Doctrine\ORM\EntityManagerInterface;

class RulesetEnum
{
    const BB_2016 = 0;

    const BB_2020 = 1;
    /**
     * @return array<int, string>
     */
    public static function stringVersNumeroBdd()
    {
        return [
            '2016' => 0,
            '2020' => 1
        ];
    }

    public static function getGameDataPlayersRepoFromIntRuleset(int $ruleset)
    {
        switch ($ruleset){
            case RulesetEnum::BB_2016:
                 return GameDataPlayers::class;
            case RulesetEnum::BB_2020:
                return GameDataPlayersBb2020::class;
        }
    }

    public static function champIdGameDataPlayerFromIntRuleset(int $ruleset)
    {
        switch ($ruleset){
            case RulesetEnum::BB_2016:
                return 'posId';
            case RulesetEnum::BB_2020:
                return'id';
        }
    }

    public static function champPositionFromIntRuleset(int $ruleset)
    {
        switch ($ruleset){
            case RulesetEnum::BB_2016:
                return 'fPos';
            case RulesetEnum::BB_2020:
                return 'fPosBb2020';
        }
    }

    /**
     * @param Teams $teams
     * @return string
     */
    public static function getGameDataPlayerRepoFromTeamByRuleset(Teams $teams): string
    {
        switch ($teams->getRuleset()){
            case RulesetEnum::BB_2016:
                return GameDataPlayers::class;
            case RulesetEnum::BB_2020:
                return GameDataPlayersBb2020::class;
        }
    }

    public static function getPositionFromPlayerByRuleset(Players $player)
    {
        switch ($player->getRuleset()){
            case RulesetEnum::BB_2016:
                return $player->getFPos();
            case RulesetEnum::BB_2020:
                return $player->getFPosBb2020();
        }
    }

    public static function getGameDataSkillRepoFromPlayerByRuleset(Players $player): string
    {
        switch ($player->getRuleset()){
            case RulesetEnum::BB_2016:
                return GameDataSkills::class;
            case RulesetEnum::BB_2020:
                return GameDataSkillsBb2020::class;
        }
    }

    public static function getGameDataPlayerRepoFromPlayerByRuleset(Players $player): string
    {
        switch ($player->getRuleset()){
            case RulesetEnum::BB_2016:
                return GameDataPlayers::class;
            case RulesetEnum::BB_2020:
                return GameDataPlayersBb2020::class;
        }
    }

    public static function getRaceFromEquipeByRuleset(Teams $equipe)
    {
        switch ($equipe->getRuleset()) {
            case RulesetEnum::BB_2016 :
                return $equipe->getFRace();
            case RulesetEnum::BB_2020:
                return $equipe->getRace();
        }
    }

    public static function getRaceFromJoueurByRuleset(Players $joueur)
    {
        switch ($joueur->getRuleset()) {
            case RulesetEnum::BB_2016 :
                return $joueur->getFRid();
            case RulesetEnum::BB_2020:
                return $joueur->getFRidBb2020();
        }
    }

    public static function setPositionAndRaceJoueurByRuleset(Players $joueur, $position)
    {
        switch ($joueur->getRuleset()) {
            case RulesetEnum::BB_2016:
                $joueur->setFPos($position);
                $joueur->setFRid($position->getfRace());
                break;
            case RulesetEnum::BB_2020:
                $joueur->setFPosBb2020($position);
                $joueur->setFRidBb2020($position->getRace());
                break;
        }

        return $joueur;
    }

    public static function getIdFromGameDataSetByRuleset(Players $joueur, $skill)
    {
        switch ($joueur->getRuleset()) {
            case RulesetEnum::BB_2016:
                 return $skill->getSkillId();
            case RulesetEnum::BB_2020:
                return $skill->getId();
        }
    }

    public static function getIconeListeFromPlayerByRuleset(Players $joueur, EntityManagerInterface $entityManager)
    {
        switch ($joueur->getRuleset()) {
            case RulesetEnum::BB_2016:
                return $entityManager->getRepository(PlayersIcons::class)->toutesLesIconesDunePosition($joueur->getFPos());
            case RulesetEnum::BB_2020:
                return $entityManager->getRepository(PlayersIcons::class)->toutesLesIconesDunePositionBb2020($joueur->getFPosBb2020());
        }
    }
}