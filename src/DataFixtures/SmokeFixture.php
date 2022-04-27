<?php


namespace App\DataFixtures;


use App\Entity\Coaches;
use App\Entity\Defis;
use App\Entity\GameDataPlayers;
use App\Entity\GameDataSkills;
use App\Entity\GameDataStadium;
use App\Entity\HistoriqueBlessure;
use App\Entity\MatchData;
use App\Entity\Matches;
use App\Entity\Meteo;
use App\Entity\Players;
use App\Entity\PlayersIcons;
use App\Entity\PlayersSkills;
use App\Entity\Races;
use App\Entity\Setting;
use App\Entity\Stades;
use App\Entity\Teams;
use App\Enum\RulesetEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Nette\Utils\DateTime;

class SmokeFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $settingYearFixture = new Setting();
        $settingYearFixture->setValue('6');
        $settingYearFixture->setName('Year');

        $settingPointsFixture = new Setting();
        $settingPointsFixture->setValue('8;3;-3');
        $settingPointsFixture->setName('points_6');

        $settingRulesetFixture = new Setting();
        $settingRulesetFixture->setValue(RulesetEnum::BB_2016);
        $settingRulesetFixture->setName('currentRuleset');

        $skillFanFavoriteFixture = new GameDataSkills();
        $skillFanFavoriteFixture->setName('Fan Favorite');

        $raceFixture = new Races();
        $raceFixture->setName('Test race');

        $coachFixture = new Coaches();
        $coachFixture->setUsername('test coach');
        $coachFixture->setRoles(['role' => 'ROLE_USER']);

        $gameDataStadiumFixture = new GameDataStadium();
        $gameDataStadiumFixture->setEffect('');
        $gameDataStadiumFixture->setType('');
        $gameDataStadiumFixture->setFamille('');

        $stadeFixture = new Stades();
        $stadeFixture->setFTypeStade($gameDataStadiumFixture);

        $equipeFixture = new Teams();
        $equipeFixture->setFRace($raceFixture);
        $equipeFixture->setOwnedByCoach($coachFixture);
        $equipeFixture->setName('Test');
        $equipeFixture->setFStades($stadeFixture);

        $defisFixture = new Defis();
        $defisFixture->setEquipeOrigine($equipeFixture);
        $defisFixture->setEquipeDefiee($equipeFixture);

        $iconeFixture = new PlayersIcons();
        $iconeFixture->setIconName('Test icone');

        $iconeNopeFixture = new PlayersIcons();
        $iconeNopeFixture->setIconName('nope');

        $positionFixture = new GameDataPlayers();
        $positionFixture->setPos('TEST position');
        $positionFixture->setFRace($raceFixture);
        $positionFixture->setCost(10_000);
        $positionFixture->setQty(16);

        $joueurFixture = new Players();
        $joueurFixture->setOwnedByTeam($equipeFixture);
        $joueurFixture->setRuleset(RulesetEnum::BB_2016);
        $joueurFixture->setIcon($iconeFixture);
        $joueurFixture->setFPos($positionFixture);
        $joueurFixture->setFRid($raceFixture);

        $meteoFixture = new Meteo();
        $meteoFixture->setNom('Test Meteo');

        $matchFixture = new Matches();
        $matchFixture->setFMeteo($meteoFixture);
        $matchFixture->setFStade($gameDataStadiumFixture);
        $matchFixture->setTeam1($equipeFixture);
        $matchFixture->setTeam2($equipeFixture);

        $historiqueBlessureFixture = new HistoriqueBlessure();
        $historiqueBlessureFixture->setDate(DateTime::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s")));
        $historiqueBlessureFixture->setPlayer($joueurFixture);
        $historiqueBlessureFixture->setFmatch($matchFixture);
        $historiqueBlessureFixture->setBlessure(30);

        $matchDataFixture = new MatchData();
        $matchDataFixture->setFMatch($matchFixture);
        $matchDataFixture->setFPlayer($joueurFixture);

        $skillFixture = new GameDataSkills();

        $playerSkillFixture = new PlayersSkills();
        $playerSkillFixture->setFPid($joueurFixture);
        $playerSkillFixture->setFSkill($skillFixture);

        $manager->persist($skillFixture);
        $manager->persist($playerSkillFixture);
        $manager->persist($matchDataFixture);
        $manager->persist($raceFixture);
        $manager->persist($meteoFixture);
        $manager->persist($gameDataStadiumFixture);
        $manager->persist($positionFixture);
        $manager->persist($iconeFixture);
        $manager->persist($iconeNopeFixture);
        $manager->persist($joueurFixture);
        $manager->persist($matchFixture);
        $manager->persist($historiqueBlessureFixture);
        $manager->persist($defisFixture);
        $manager->persist($stadeFixture);
        $manager->persist($equipeFixture);
        $manager->persist($coachFixture);
        $manager->persist($skillFanFavoriteFixture);
        $manager->persist($settingYearFixture);
        $manager->persist($settingPointsFixture);
        $manager->persist($settingRulesetFixture);

        $manager->flush();
    }
}