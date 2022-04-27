<?php

namespace App\Tests\src\Service\PlayerService;

use App\Entity\GameDataPlayers;
use App\Entity\GameDataPlayersBb2020;
use App\Entity\GameDataSkills;
use App\Entity\Players;
use App\Service\EquipeGestionService;
use App\Service\EquipeService;
use App\Service\InfosService;
use App\Service\MatchDataService;
use App\Service\PlayerService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class listeDesCompdDeBasedUnJoueurTest extends TestCase
{
    /**
     * @test
     */
    public function toutes_les_comps_de_base_sont_retournees_bb2016(): void
    {
        $baseSkillsTest = new ArrayCollection();

        $gameDataPlayerMock = $this->createMock(GameDataPlayers::class);
        $gameDataPlayerMock->method('getBaseSkills')->willReturn($baseSkillsTest);

        $joueurMock = $this->createMock(Players::class);
        $joueurMock->method('getFPos')->willReturn($gameDataPlayerMock);
        $joueurMock->method('getRuleset')->willReturn(0);

        $gameDataSkillMock0 = $this->createMock(GameDataSkills::class);
        $gameDataSkillMock0->method('getName')->willReturnOnConsecutiveCalls('Frenzy');

        $gameDataSkillMock1 = $this->createMock(GameDataSkills::class);
        $gameDataSkillMock1->method('getName')->willReturnOnConsecutiveCalls('Dodge');

        $gameDataSkillMock2 = $this->createMock(GameDataSkills::class);
        $gameDataSkillMock2->method('getName')->willReturnOnConsecutiveCalls('Jump Up');

        $baseSkillsTest->add($gameDataSkillMock0);
        $baseSkillsTest->add($gameDataSkillMock1);
        $baseSkillsTest->add($gameDataSkillMock2);

        $gameDataSkillRepoMock = $this->createMock(ObjectRepository::class);
        $gameDataSkillRepoMock->method('findOneBy')->willReturnOnConsecutiveCalls(
            $gameDataSkillMock0,
            $gameDataSkillMock1,
            $gameDataSkillMock2
        );

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($gameDataSkillRepoMock);

        $matchDataService = new MatchDataService($objectManager);

        $playerService = new PlayerService(
            $objectManager,
            $this->createMock(EquipeGestionService::class),
            $matchDataService,
            $this->createMock(InfosService::class)
        );

        $retour = '<text class="test-primary">Frenzy</text>, <text class="test-primary">Dodge</text>, <text class="test-primary">Jump Up</text>, ';

        $this->assertEquals($retour, $playerService->listeDesCompdDeBasedUnJoueur($joueurMock));
    }

    /**
     * @test
     */
    public function toutes_les_comps_de_base_sont_retournees_bb2020(): void
    {
        $baseSkillsTest = new ArrayCollection();

        $gameDataPlayerMock = $this->createMock(GameDataPlayersBb2020::class);
        $gameDataPlayerMock->method('getBaseSkills')->willReturn($baseSkillsTest);

        $joueurMock = $this->createMock(Players::class);
        $joueurMock->method('getFPosBb2020')->willReturn($gameDataPlayerMock);
        $joueurMock->method('getRuleset')->willReturn(1);

        $gameDataSkillMock0 = $this->createMock(GameDataSkills::class);
        $gameDataSkillMock0->method('getName')->willReturnOnConsecutiveCalls('Frenzy');

        $gameDataSkillMock1 = $this->createMock(GameDataSkills::class);
        $gameDataSkillMock1->method('getName')->willReturnOnConsecutiveCalls('Dodge');

        $gameDataSkillMock2 = $this->createMock(GameDataSkills::class);
        $gameDataSkillMock2->method('getName')->willReturnOnConsecutiveCalls('Jump Up');

        $baseSkillsTest->add($gameDataSkillMock0);
        $baseSkillsTest->add($gameDataSkillMock1);
        $baseSkillsTest->add($gameDataSkillMock2);

        $gameDataSkillRepoMock = $this->createMock(ObjectRepository::class);
        $gameDataSkillRepoMock->method('findOneBy')->willReturnOnConsecutiveCalls(
            $gameDataSkillMock0,
            $gameDataSkillMock1,
            $gameDataSkillMock2
        );

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($gameDataSkillRepoMock);

        $matchDataService = new MatchDataService($objectManager);

        $playerService = new PlayerService(
            $objectManager,
            $this->createMock(EquipeGestionService::class),
            $matchDataService,
            $this->createMock(InfosService::class)
        );

        $retour = '<text class="test-primary">Frenzy</text>, <text class="test-primary">Dodge</text>, <text class="test-primary">Jump Up</text>, ';

        $this->assertEquals($retour, $playerService->listeDesCompdDeBasedUnJoueur($joueurMock));
    }
}