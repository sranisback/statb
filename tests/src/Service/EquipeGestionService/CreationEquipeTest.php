<?php


namespace App\Tests\src\Service\EquipeGestionService;


use App\Entity\Coaches;
use App\Entity\GameDataStadium;
use App\Entity\Races;
use App\Entity\RacesBb2020;
use App\Entity\Stades;
use App\Entity\Teams;
use App\Enum\RulesetEnum;
use App\Service\EquipeGestionService;
use App\Service\InducementService;
use App\Service\InfosService;
use App\Service\SettingsService;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;

class CreationEquipeTest extends TestCase
{
    /**
     * @test
     */
    public function une_equipe_bb2016_est_cree() : void
    {
        $raceMock = $this->createMock(Races::class);

        $raceRepoMock = $this->getMockBuilder(Races::class)
            ->addMethods(['findOneBy'])
            ->getMock();
        $raceRepoMock->method('findOneBy')->willReturn($raceMock);

        $coachMock = $this->createMock(Coaches::class);

        $coachRepoMock = $this->getMockBuilder(Coaches::class)
            ->addMethods(['findOneBy'])
            ->getMock();
        $coachRepoMock->method('findOneBy')->willReturn($coachMock);

        $gameDataStadiumMock = $this->createMock(GameDataStadium::class);

        $gameDataStadiumRepoMock = $this->getMockBuilder(GameDataStadium::class)
            ->addMethods(['findOneBy'])
            ->getMock();
        $gameDataStadiumRepoMock->method('findOneBy')->willReturn($gameDataStadiumMock);

        $objectManager = $this->createMock(EntityManager::class);
        $objectManager->method('getRepository')->will($this->returnCallback(
            function ($entityName) use ($raceRepoMock,$coachRepoMock,$gameDataStadiumRepoMock) {
                if ($entityName === Races::class) {
                    return $raceRepoMock;
                }

                if ($entityName === Coaches::class) {
                    return $coachRepoMock;
                }

                if($entityName === GameDataStadium::class) {
                    return $gameDataStadiumRepoMock;
                }
                return true;
            }
        ));

        $stadeTest = new Stades();
        $stadeTest->setFTypeStade($gameDataStadiumMock);
        $stadeTest->setTotalPayement(0);
        $stadeTest->setNom('La prairie verte');
        $stadeTest->setNiveau(0);

        $teamExpected = new Teams();
        $teamExpected->setName('TEST');
        $teamExpected->setFRace($raceMock);
        $teamExpected->setOwnedByCoach($coachMock);
        $teamExpected->setTreasury(1_000_000);
        $teamExpected->setTv(0);
        $teamExpected->setFStades($stadeTest);
        $teamExpected->setRuleset(RulesetEnum::BB_2016);
        $teamExpected->setYear(7);
        $teamExpected->setElo(150);
        $teamExpected->setFf(0);

        $settingServiceMock = $this->createMock(SettingsService::class);
        $settingServiceMock->method('recupererTresorDepart')->willReturn(1_000_000);
        $settingServiceMock->method('anneeCourante')->willReturn(7);

        $equipeGestionServiceTest = new EquipeGestionService(
            $objectManager,
            $settingServiceMock,
            $this->createMock(InfosService::class),
            $this->createMock(InducementService::class)
        );

        $objectManager->expects($this->exactly(1))->method('refresh')->with($teamExpected);

        $equipeGestionServiceTest->creationEquipe(
                RulesetEnum::BB_2016,
                1,
                1,
                "TEST"
        );
    }

    /**
     * @test
     */
    public function une_equipe_bb2020_est_cree() : void
    {
        $raceMock = $this->createMock(RacesBb2020::class);

        $raceRepoMock = $this->getMockBuilder(RacesBb2020::class)
            ->addMethods(['findOneBy'])
            ->getMock();
        $raceRepoMock->method('findOneBy')->willReturn($raceMock);

        $coachMock = $this->createMock(Coaches::class);

        $coachRepoMock = $this->getMockBuilder(Coaches::class)
            ->addMethods(['findOneBy'])
            ->getMock();
        $coachRepoMock->method('findOneBy')->willReturn($coachMock);

        $gameDataStadiumMock = $this->createMock(GameDataStadium::class);

        $gameDataStadiumRepoMock = $this->getMockBuilder(GameDataStadium::class)
            ->addMethods(['findOneBy'])
            ->getMock();
        $gameDataStadiumRepoMock->method('findOneBy')->willReturn($gameDataStadiumMock);

        $objectManager = $this->createMock(EntityManager::class);
        $objectManager->method('getRepository')->will($this->returnCallback(
            function ($entityName) use ($raceRepoMock,$coachRepoMock,$gameDataStadiumRepoMock) {
                if ($entityName === RacesBb2020::class) {
                    return $raceRepoMock;
                }

                if ($entityName === Coaches::class) {
                    return $coachRepoMock;
                }

                if($entityName === GameDataStadium::class) {
                    return $gameDataStadiumRepoMock;
                }
                return true;
            }
        ));

        $stadeTest = new Stades();
        $stadeTest->setFTypeStade($gameDataStadiumMock);
        $stadeTest->setTotalPayement(0);
        $stadeTest->setNom('La prairie verte');
        $stadeTest->setNiveau(0);

        $teamExpected = new Teams();
        $teamExpected->setName('TEST');
        $teamExpected->setRace($raceMock);
        $teamExpected->setOwnedByCoach($coachMock);
        $teamExpected->setTreasury(1_000_000);
        $teamExpected->setTv(0);
        $teamExpected->setFStades($stadeTest);
        $teamExpected->setRuleset(RulesetEnum::BB_2020);
        $teamExpected->setYear(7);
        $teamExpected->setElo(150);
        $teamExpected->setFf(1);

        $settingServiceMock = $this->createMock(SettingsService::class);
        $settingServiceMock->method('recupererTresorDepart')->willReturn(1_000_000);
        $settingServiceMock->method('anneeCourante')->willReturn(7);

        $equipeGestionServiceTest = new EquipeGestionService(
            $objectManager,
            $settingServiceMock,
            $this->createMock(InfosService::class),
            $this->createMock(InducementService::class)
        );

        $objectManager->expects($this->exactly(1))->method('refresh')->with($teamExpected);

        $equipeGestionServiceTest->creationEquipe(
            RulesetEnum::BB_2020,
            1,
            1,
            "TEST"
        );
    }
}