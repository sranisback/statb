<?php

namespace App\Tests\src\Service\PlayerService;

use App\Entity\GameDataPlayers;
use App\Entity\GameDataSkills;
use App\Entity\MatchData;
use App\Entity\Players;
use App\Entity\PlayersSkills;
use App\Service\EquipeService;
use App\Service\InfosService;
use App\Service\MatchDataService;
use App\Service\PlayerService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class statsDuJoueurTest extends TestCase
{
    /**
     * @test
     */
    public function les_stats_du_joueur_sont_bien_retournee(): void
    {
        $matchDataMock0 = $this->createMock(MatchData::class);
        $matchDataMock0->method('getMvp')->willReturn(1);

        $matchDataRepoMock = $this->createMock(ObjectRepository::class);
        $matchDataRepoMock->method('findBy')->willReturn([$matchDataMock0]);

        $gameDataSkillMock = $this->createMock(GameDataSkills::class);
        $gameDataSkillMock->method('getName')->willReturn('Block');

        $gameDataSkillMock0 = $this->createMock(GameDataSkills::class);
        $gameDataSkillMock0->method('getName')->willReturnOnConsecutiveCalls('Frenzy');

        $gameDataSkillMock1 = $this->createMock(GameDataSkills::class);
        $gameDataSkillMock1->method('getName')->willReturnOnConsecutiveCalls('Dodge');

        $gameDataSkillMock2 = $this->createMock(GameDataSkills::class);
        $gameDataSkillMock2->method('getName')->willReturnOnConsecutiveCalls('Jump Up');

        $baseSkillsTest = new ArrayCollection();

        $baseSkillsTest->add($gameDataSkillMock0);
        $baseSkillsTest->add($gameDataSkillMock1);
        $baseSkillsTest->add($gameDataSkillMock2);

        $positionmock = $this->createMock(GameDataPlayers::class);
        $positionmock->method('getBaseSkills')->willReturn($baseSkillsTest);

        $skillAdded = new ArrayCollection();

        $joueurMock = $this->createMock(Players::class);
        $joueurMock->method('getFPos')->willReturn($positionmock);
        $joueurMock->method('getJournalier')->willReturn(false);
        $joueurMock->method('getSkills')->willReturn($skillAdded);

        $playersSkillsMock = $this->createMock(PlayersSkills::class);
        $playersSkillsMock->method('getType')->willReturn('N');
        $playersSkillsMock->method('getFSkill')->willReturn($gameDataSkillMock);

        $skillAdded->add($playersSkillsMock);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchDataRepoMock);

        $playerService = new PlayerService(
            $objectManager,
            $this->createMock(EquipeService::class),
            $this->createMock(MatchDataService::class),
            $this->createMock(InfosService::class)
        );

        $retour['comp'] = '<text class="test-primary">Frenzy</text>, <text class="test-primary">Dodge</text>, <text class="test-primary">Jump Up</text>, <text class="text-success">Block</text>';
        $retour['actions'] = [
            'NbrMatch' => 1,
            'cp' => 0,
            'td' => 0,
            'int' => 0,
            'cas' => 0,
            'mvp' => 1,
            'agg' => 0,
        ];

        $this->assertEquals($retour, $playerService->statsDuJoueur($joueurMock));
    }
}