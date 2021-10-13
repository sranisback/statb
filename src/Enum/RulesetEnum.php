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
    public static function numeroVersEtiquette() : array
    {
        return [
            0 => 'Bb 2016',
            1 => 'Bb 2020'
        ];
    }
    
    public static function rulesetParAnnee() : array
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

    /**
     * @param int|string $ruleset
     * @return class-string|null
     */
    public static function getGameDataPlayersRepoFromIntRuleset($ruleset): ?string
    {
        switch ($ruleset){
            case RulesetEnum::BB_2016:
                 return GameDataPlayers::class;
            case RulesetEnum::BB_2020:
                return GameDataPlayersBb2020::class;
            default:
                return null;
        }
    }

    public static function champIdGameDataPlayerFromIntRuleset(int $ruleset) : ?string
    {
        switch ($ruleset){
            case RulesetEnum::BB_2016:
                return 'posId';
            case RulesetEnum::BB_2020:
                return'id';
            default:
                return null;
        }
    }

    public static function champPositionFromIntRuleset(int $ruleset) : ?string
    {
        switch ($ruleset){
            case RulesetEnum::BB_2016:
                return 'fPos';
            case RulesetEnum::BB_2020:
                return 'fPosBb2020';
            default:
                return null;
        }
    }

    /**
     * @param Teams $teams
     * @return string
     */
    public static function getGameDataPlayerRepoFromTeamByRuleset(Teams $teams) : string
    {
        switch ($teams->getRuleset()){
            case RulesetEnum::BB_2016:
                return GameDataPlayers::class;
            case RulesetEnum::BB_2020:
                return GameDataPlayersBb2020::class;
            default:
                return 'erreur';
        }
    }

    /**
     * @param Players $player
     * @return GameDataPlayers|GameDataPlayersBb2020|null
     */
    public static function getPositionFromPlayerByRuleset(Players $player)
    {
        switch ($player->getRuleset()){
            case RulesetEnum::BB_2016:
                return $player->getFPos();
            case RulesetEnum::BB_2020:
                return $player->getFPosBb2020();
            default:
                return null;
        }
    }

    /**
     * @param Players $player
     * @return class-string|null
     */
    public static function getGameDataSkillRepoFromPlayerByRuleset(Players $player) : ?string
    {
        switch ($player->getRuleset()){
            case RulesetEnum::BB_2016:
                return GameDataSkills::class;
            case RulesetEnum::BB_2020:
                return GameDataSkillsBb2020::class;
            default:
                return null;
        }
    }

    public static function getGameDataSkillRepoFromIntByRuleset(int $ruleset): string
    {
        switch ($ruleset){
            case RulesetEnum::BB_2016:
                return GameDataSkills::class;
            case RulesetEnum::BB_2020:
                return GameDataSkillsBb2020::class;
            default:
                return 'erreur';
        }
    }

    public static function getGameDataPlayerRepoFromPlayerByRuleset(Players $player): string
    {
        switch ($player->getRuleset()){
            case RulesetEnum::BB_2016:
                return GameDataPlayers::class;
            case RulesetEnum::BB_2020:
                return GameDataPlayersBb2020::class;
            default:
                return 'erreur';
        }
    }

    public static function getGameDataPlayerChampIdFromPlayerByRuleset(Players $player): string
    {
        switch ($player->getRuleset()){
            case RulesetEnum::BB_2016:
                return 'skillId';
            case RulesetEnum::BB_2020:
                return 'id';
            default:
                return 'erreur';
        }
    }

    /* @phpstan-ignore-next-line */
    public static function getRaceFromEquipeByRuleset(Teams $equipe)
    {
        switch ($equipe->getRuleset()) {
            case RulesetEnum::BB_2016 :
                return $equipe->getFRace();
            case RulesetEnum::BB_2020:
                return $equipe->getRace();
            default:
                return null;
        }
    }

    /* @phpstan-ignore-next-line */
    public static function getRaceFromJoueurByRuleset(Players $joueur)
    {
        switch ($joueur->getRuleset()) {
            case RulesetEnum::BB_2016 :
                return $joueur->getFRid();
            case RulesetEnum::BB_2020:
                return $joueur->getFRidBb2020();
            default:
                return null;
        }
    }

    /**
     * @param Players $joueur
     * @param GameDataPlayersBb2020|GameDataPlayers $position
     * @return Players
     */
    public static function setPositionAndRaceJoueurByRuleset(Players $joueur, $position) : Players
    {
        switch ($joueur->getRuleset()) {
            case RulesetEnum::BB_2016:
                /* @phpstan-ignore-next-line */
                $joueur->setFPos($position);
                /* @phpstan-ignore-next-line */
                $joueur->setFRid($position->getfRace());
                break;
            case RulesetEnum::BB_2020:
                /* @phpstan-ignore-next-line */
                $joueur->setFPosBb2020($position);
                /* @phpstan-ignore-next-line */
                $joueur->setFRidBb2020($position->getRace());
                break;
            default:
                break;
        }

        return $joueur;
    }

    /**
     * @param Players $joueur
     * @param GameDataSkills|GameDataPlayersBb2020 $skill
     * @return int|null
     */
    public static function getIdFromGameDataSetByRuleset(Players $joueur, $skill) : ?int
    {
        switch ($joueur->getRuleset()) {
            case RulesetEnum::BB_2016:
                /* @phpstan-ignore-next-line */
                 return $skill->getSkillId();
            case RulesetEnum::BB_2020:
                /* @phpstan-ignore-next-line */
                return $skill->getId();
            default:
                return null;
        }
    }

    public static function getIconeListeFromPlayerByRuleset(Players $joueur, EntityManagerInterface $entityManager) : ?array
    {
        switch ($joueur->getRuleset()) {
            case RulesetEnum::BB_2016:
                return $entityManager->getRepository(PlayersIcons::class)->toutesLesIconesDunePosition($joueur->getFPos());
            case RulesetEnum::BB_2020:
                return $entityManager->getRepository(PlayersIcons::class)->toutesLesIconesDunePositionBb2020($joueur->getFPosBb2020());
            default:
                return null;
        }
    }

    /**
     * @param Teams $team
     * @param RacesBb2020|Races $race
     * @return Teams|null
     */
    public static function setTeamRaceFromTeamByRuleset(Teams $team, $race) : ?Teams
    {
        switch ($team->getRuleset()){
            case RulesetEnum::BB_2016:
                /* @phpstan-ignore-next-line */
                return $team->setfRace($race);
            case RulesetEnum::BB_2020:
                /* @phpstan-ignore-next-line */
                return $team->setRace($race);
            default:
                return null;
        }
    }

    public static function setFanFactorFromTeamByRuleset(Teams $team) : ?Teams
    {
        switch ($team->getRuleset()){
            case RulesetEnum::BB_2016:
                return $team->setFf(0);
            case RulesetEnum::BB_2020:
                return $team->setFf(1);
            default:
                return null;
        }
    }

    /**
     * @param int|string $ruleset
     * @return string|null
     */
    public static function getChampRaceFromIntByRuleset($ruleset)
    {
        switch ($ruleset){
            case RulesetEnum::BB_2016:
                return 'fRace';
            case RulesetEnum::BB_2020:
                return 'race';
            default:
                return null;
        }
    }

    /**
     * @param int $ruleset
     * @return class-string|null
     */
    public static function getRaceRepoFromIntByRuleset(int $ruleset) : ?string
    {
        switch ($ruleset){
            case RulesetEnum::BB_2016:
                return Races::class;
            case RulesetEnum::BB_2020:
                return RacesBb2020::class;
            default:
                return null;
        }
    }

    public static function getRaceIdFromIntByRuleset(int $ruleset) : ?string
    {
        switch ($ruleset){
            case RulesetEnum::BB_2016:
                return 'raceId';
            case RulesetEnum::BB_2020:
                return 'id';
            default:
                return null;
        }
    }

    /**
     * @param int $ruleset
     * @return string|null
     */
    public static function getChampSkillFromIntByRuleset(int $ruleset): ?string
    {
        switch ($ruleset){
            case RulesetEnum::BB_2016:
                return 'fSkill';
            case RulesetEnum::BB_2020:
                return 'fSkillBb2020';
            default:
                return null;
        }
    }
}