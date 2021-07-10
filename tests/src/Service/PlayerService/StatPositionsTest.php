<?php


namespace App\Tests\src\Service\PlayerService;


use App\Entity\GameDataPlayers;
use App\Entity\GameDataSkills;
use App\Service\EquipeService;
use App\Service\InfosService;
use App\Service\MatchDataService;
use App\Service\PlayerService;
use Doctrine\Common\Collections\ArrayCollection;
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
        $baseSkillsTest = new ArrayCollection();

        $gameDataPlayerMock = $this->createMock(GameDataPlayers::class);
        $gameDataPlayerMock->method('getBaseSkills')->willReturn($baseSkillsTest);

        $gameDateSkillMock = $this->createMock(GameDataSkills::class);
        $gameDateSkillMock->method('getName')->willReturn('Block');

        $baseSkillsTest->add($gameDateSkillMock);

        $playerServiceTest = new PlayerService(
            $this->createMock(EntityManager::class),
            $this->createMock(EquipeService::class),
            $this->createMock(MatchDataService::class),
            $this->createMock(InfosService::class)
        );

        $this->assertEquals(
            '<text class="test-primary">Block</text>',
            $playerServiceTest->competencesDunePositon($gameDataPlayerMock)
        );
    }

    /**
     * @test
     */
    public function la_position_a_plusieur_comp()
    {
        $baseSkillsTest = new ArrayCollection();

        $gameDataPlayerMock = $this->createMock(GameDataPlayers::class);
        $gameDataPlayerMock->method('getBaseSkills')->willReturn($baseSkillsTest);

        $gameDateSkillMock0 = $this->createMock(GameDataSkills::class);
        $gameDateSkillMock0->method('getName')->willReturn('Block');

        $gameDateSkillMock1 = $this->createMock(GameDataSkills::class);
        $gameDateSkillMock1->method('getName')->willReturn('Guard');

        $baseSkillsTest->add($gameDateSkillMock0);
        $baseSkillsTest->add($gameDateSkillMock1);

        $playerServiceTest = new PlayerService(
            $this->createMock(EntityManager::class),
            $this->createMock(EquipeService::class),
            $this->createMock(MatchDataService::class),
            $this->createMock(InfosService::class)
        );

        $this->assertEquals(
            '<text class="test-primary">Block</text>, <text class="test-primary">Guard</text>',
            $playerServiceTest->competencesDunePositon($gameDataPlayerMock)
        );
    }

    /**
     * @test
     */
    public function la_position_n_a_pas_de_comp()
    {
        $baseSkillsTest = new ArrayCollection();

        $gameDataPlayerMock = $this->createMock(GameDataPlayers::class);
        $gameDataPlayerMock->method('getBaseSkills')->willReturn($baseSkillsTest);

        $playerServiceTest = new PlayerService(
            $this->createMock(EntityManager::class),
            $this->createMock(EquipeService::class),
            $this->createMock(MatchDataService::class),
            $this->createMock(InfosService::class)
        );

        $this->assertEquals(
            '',
            $playerServiceTest->competencesDunePositon($gameDataPlayerMock)
        );
    }
}