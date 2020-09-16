<?php


namespace App\Tests\src\Service\PlayerService;


use App\Entity\GameDataPlayers;
use App\Entity\GameDataSkills;
use App\Entity\Players;
use App\Service\EquipeService;
use App\Service\MatchDataService;
use App\Service\PlayerService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class LeJoueurEstDisposableTest extends TestCase
{
    /**
     * @test
     */
    public function le_joueur_est_disposable()
    {
        $gameDataSkillsTest = $this->createMock(GameDataSkills::class);
        $gameDataSkillsTest->method('getName')->willReturn('Disposable');
        $gameDataSkillsTest->method('getSkillId')->willReturn(1);

        $positionTest = new GameDataPlayers();
        $positionTest->setCost(50_000);
        $positionTest->setSkills(1);

        $joueurTest = new Players();
        $joueurTest->setFPos($positionTest);

        $playerRepoMock = $this->getMockBuilder(Players::class)
            ->setMethods(['findOneBy'])
            ->getMock();
        $playerRepoMock->method('findOneBy')->willReturn($gameDataSkillsTest);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($playerRepoMock);

        $playerServiceTest = new PlayerService(
            $objectManager,
            $this->createMock(EquipeService::class),
            $this->createMock(MatchDataService::class)
        );

        $this->assertTrue($playerServiceTest->leJoueurEstDisposable($joueurTest));
    }

    /**
     * @test
     */
    public function le_joueur_est_pas_disposable()
    {
        $gameDataSkillsTest = $this->createMock(GameDataSkills::class);
        $gameDataSkillsTest->method('getName')->willReturn('Disposable');
        $gameDataSkillsTest->method('getSkillId')->willReturn(1);

        $positionTest = new GameDataPlayers();
        $positionTest->setCost(50_000);
        $positionTest->setSkills(50);

        $joueurTest = new Players();
        $joueurTest->setFPos($positionTest);

        $playerRepoMock = $this->getMockBuilder(Players::class)
            ->setMethods(['findOneBy'])
            ->getMock();
        $playerRepoMock->method('findOneBy')->willReturn($gameDataSkillsTest);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($playerRepoMock);

        $playerServiceTest = new PlayerService(
            $objectManager,
            $this->createMock(EquipeService::class),
            $this->createMock(MatchDataService::class)
        );

        $this->assertFalse($playerServiceTest->leJoueurEstDisposable($joueurTest));
    }

}