<?php


namespace App\Enum;


use App\Entity\GameDataPlayers;
use App\Entity\GameDataPlayersBb2020;
use App\Entity\GameDataSkills;
use App\Entity\GameDataSkillsBb2020;
use App\Entity\Players;
use App\Entity\PlayersIcons;
use App\Entity\Races;
use App\Entity\RacesBb2020;
use App\Entity\Teams;
use Doctrine\ORM\EntityManagerInterface;

class RulesetEnum
{
    const BB_2016 = 0;

    const BB_2020 = 1;
    /**
     * @return array<int, string>
     */
    public static function numeroVersEtiquette()
    {
        return [
            0 => 'Bb 2016',
            1 => 'Bb 2020'
        ];
    }
    
    public static function rulesetParAnnee()
    {
        return [
            0 => 0,
            1 => 0,
            2 => 0,
            3 => 0,
            4 => 0,
            5 => 0,
            6 => 0,
            7 => 1
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

    public static function getGameDataSkillRepoFromIntByRuleset(int $ruleset): string
    {
        switch ($ruleset){
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

    public static function getGameDataPlayerChampIdFromPlayerByRuleset(Players $player): string
    {
        switch ($player->getRuleset()){
            case RulesetEnum::BB_2016:
                return 'skillId';
            case RulesetEnum::BB_2020:
                return 'id';
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

    public static function setTeamRaceFromTeamByRuleset(Teams $team, $race)
    {
        switch ($team->getRuleset()){
            case RulesetEnum::BB_2016:
                return $team->setfRace($race);
            case RulesetEnum::BB_2020:
                return $team->setRace($race);
        }
    }

    public static function getChampRaceFromIntByRuleset(int $ruleset)
    {
        switch ($ruleset){
            case RulesetEnum::BB_2016:
                return 'fRace';
            case RulesetEnum::BB_2020:
                return 'race';
        }
    }

    public static function getRaceRepoFromIntByRuleset(int $ruleset)
    {
        switch ($ruleset){
            case RulesetEnum::BB_2016:
                return Races::class;
            case RulesetEnum::BB_2020:
                return RacesBb2020::class;
        }
    }

    public static function getRaceIdFromIntByRuleset(int $ruleset)
    {
        switch ($ruleset){
            case RulesetEnum::BB_2016:
                return 'raceId';
            case RulesetEnum::BB_2020:
                return 'id';
        }
    }

    public static function getChampSkillFromIntByRuleset(int $ruleset)
    {
        switch ($ruleset){
            case RulesetEnum::BB_2016:
                return 'fSkill';
            case RulesetEnum::BB_2020:
                return 'fSkillBb2020';
        }
    }
}