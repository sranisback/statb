<?php


namespace App\Tests\src\Service\PlayerService;


use App\Entity\GameDataPlayers;
use App\Entity\GameDataSkills;
use App\Service\EquipeService;
use App\Service\MatchDataService;
use App\Service\PlayerService;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;

class StatPositionsTest extends TestCase
{
    /**
     * @test
     */
    public function la_position_a_une_seul_comp()
    {
        $gameDataPlayerTest = $this->createMock(GameDataPlayers::class);
        $gameDataPlayerTest->method('getSkills')->willReturn('1');

        $gameDateSkillTest = $this->createMock(GameDataSkills::class);
        $gameDateSkillTest->method('getName')->willReturn('Block');

        $gameDataSkillRepoMock = $this->createMock(ObjectRepository::class);
        $gameDataSkillRepoMock->method('findOneBy')->willReturn($gameDateSkillTest);

        $objectManager = $this->createMock(EntityManager::class);
        $objectManager->method('getRepository')->willReturn($gameDataSkillRepoMock);

        $playerServiceTest = new PlayerService(
            $objectManager,
            $this->createMock(EquipeService::class),
            $this->createMock(MatchDataService::class)
        );

        $this->assertEquals(
            '<text class="test-primary">Block</text>',
            $playerServiceTest->competencesDunePositon($gameDataPlayerTest)
        );
    }

    /**
     * @test
     */
    public function la_position_a_plusieur_comp()
    {
        $gameDataPlayerTest = $this->createMock(GameDataPlayers::class);
        $gameDataPlayerTest->method('getSkills')->willReturn('1,2');

        $gameDateSkillTest0 = $this->createMock(GameDataSkills::class);
        $gameDateSkillTest0->method('getName')->willReturn('Block');

        $gameDateSkillTest1 = $this->createMock(GameDataSkills::class);
        $gameDateSkillTest1->method('getName')->willReturn('Guard');

        $gameDataSkillRepoMock = $this->createMock(ObjectRepository::class);
        $gameDataSkillRepoMock->method('findOneBy')->willReturnOnConsecutiveCalls(
            $gameDateSkillTest0,
            $gameDateSkillTest1
        );

        $objectManager = $this->createMock(EntityManager::class);
        $objectManager->method('getRepository')->willReturn($gameDataSkillRepoMock);

        $playerServiceTest = new PlayerService(
            $objectManager,
            $this->createMock(EquipeService::class),
            $this->createMock(MatchDataService::class)
        );

        $this->assertEquals(
            '<text class="test-primary">Block</text>, <text class="test-primary">Guard</text>',
            $playerServiceTest->competencesDunePositon($gameDataPlayerTest)
        );
    }

    /**
     * @test
     */
    public function la_position_n_a_pas_de_comp()
    {
        $gameDataPlayerTest = $this->createMock(GameDataPlayers::class);
        $gameDataPlayerTest->method('getSkills')->willReturn('');

        $playerServiceTest = new PlayerService(
            $this->createMock(EntityManager::class),
            $this->createMock(EquipeService::class),
            $this->createMock(MatchDataService::class)
        );

        $this->assertEquals(
            '',
            $playerServiceTest->competencesDunePositon($gameDataPlayerTest)
        );
    }
}