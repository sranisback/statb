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
use App\Tests\src\TestServiceFactory\EquipeGestionServiceTestFactory;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;

class CreationEquipeTest extends TestCase
{
    
    private Teams $equipeAttendue;

    private $objectManager;

    private EquipeGestionService $equipeGestionService;

    private $raceMock;

    private $raceBb2020Mock;

    public function setUp(): void
    {
        parent::setUp();

        $gameDataStadiumMock = $this->createMock(GameDataStadium::class);

        $gameDataStadiumRepoMock = $this->getMockBuilder(GameDataStadium::class)
            ->addMethods(['findOneBy'])
            ->getMock();
        $gameDataStadiumRepoMock->method('findOneBy')->willReturn($gameDataStadiumMock);

        $this->raceMock = $this->createMock(Races::class);

        $raceRepoMock = $this->getMockBuilder(Races::class)
            ->addMethods(['findOneBy'])
            ->getMock();
        $raceRepoMock->method('findOneBy')->willReturn($this->raceMock);

        $this->raceBb2020Mock = $this->createMock(RacesBb2020::class);

        $raceBb202RepoMock = $this->getMockBuilder(RacesBb2020::class)
            ->addMethods(['findOneBy'])
            ->getMock();
        $raceBb202RepoMock->method('findOneBy')->willReturn($this->raceBb2020Mock);

        $coachMock = $this->createMock(Coaches::class);

        $coachRepoMock = $this->getMockBuilder(Coaches::class)
            ->addMethods(['findOneBy'])
            ->getMock();
        $coachRepoMock->method('findOneBy')->willReturn($coachMock);

        $stadeTest = new Stades();
        $stadeTest->setFTypeStade($gameDataStadiumMock);
        $stadeTest->setTotalPayement(0);
        $stadeTest->setNom('La prairie verte');
        $stadeTest->setNiveau(0);

        $this->equipeAttendue = new Teams();
        $this->equipeAttendue->setName('TEST');
        $this->equipeAttendue->setOwnedByCoach($coachMock);
        $this->equipeAttendue->setTreasury(1_000_000);
        $this->equipeAttendue->setTv(0);
        $this->equipeAttendue->setFStades($stadeTest);
        $this->equipeAttendue->setYear(7);
        $this->equipeAttendue->setElo(150);
        $this->equipeAttendue->setScore(100);

        $this->objectManager = $this->createMock(EntityManager::class);
        $this->objectManager->method('getRepository')->will($this->returnCallback(
            function ($entityName) use ($raceRepoMock,$coachRepoMock,$gameDataStadiumRepoMock,$raceBb202RepoMock) {
                if ($entityName === Races::class) {
                    return $raceRepoMock;
                }

                if ($entityName === RacesBb2020::class) {
                    return $raceBb202RepoMock;
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

        $settingServiceMock = $this->createMock(SettingsService::class);
        $settingServiceMock->method('recupererTresorDepart')->willReturn(1_000_000);
        $settingServiceMock->method('anneeCourante')->willReturn(7);

        $this->equipeGestionService = (new EquipeGestionServiceTestFactory())->getInstance(
            $this->objectManager,
            $settingServiceMock
        );
    }

    /**
     * @test
     */
    public function une_equipe_bb2016_est_cree() : void
    {
        $this->equipeAttendue->setFf(0);
        $this->equipeAttendue->setRuleset(RulesetEnum::BB_2016);
        $this->equipeAttendue->setFRace($this->raceMock);

        $this->objectManager->expects($this->exactly(1))->method('refresh')->with($this->equipeAttendue);

        $this->equipeGestionService->creationEquipe(
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
        $this->equipeAttendue->setFf(1);
        $this->equipeAttendue->setRuleset(RulesetEnum::BB_2020);
        $this->equipeAttendue->setRace($this->raceBb2020Mock);

        $this->objectManager->expects($this->exactly(1))->method('refresh')->with($this->equipeAttendue);

        $this->equipeGestionService->creationEquipe(
            RulesetEnum::BB_2020,
            1,
            1,
            "TEST"
        );
    }
}