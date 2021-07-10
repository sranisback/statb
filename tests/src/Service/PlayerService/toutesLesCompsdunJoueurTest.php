<?php

namespace App\Tests\src\Service\PlayerService;


use App\Entity\GameDataPlayers;
use App\Entity\GameDataSkills;
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

class toutesLesCompsdunJoueurTest extends TestCase
{

    /**
     * @test
     */
    public function toutes_les_comps_sont_retournees(): void
    {
        $gameDataSkillMock0 = $this->createMock(GameDataSkills::class);
        $gameDataSkillMock0->method('getName')->willReturn('Test');

        $gameDataSkillMock1 = $this->createMock(GameDataSkills::class);
        $gameDataSkillMock1->method('getName')->willReturn('Test');

        $playerSkillMock = $this->createMock(PlayersSkills::class);
        $playerSkillMock->method('getFSkill')->willReturn($gameDataSkillMock1);

        $baseCompMock = new ArrayCollection([$gameDataSkillMock0]);
        $skillAdded = new ArrayCollection([$playerSkillMock]);

        $positionMock = $this->createMock(GameDataPlayers::class);
        $positionMock->method('getBaseSkills')->willReturn($baseCompMock);

        $joueurMock = $this->createMock(Players::class);
        $joueurMock->method('getFPos')->willReturn($positionMock);
        $joueurMock->method('getSkills')->willReturn($skillAdded);

        $playersSkillsRepoMock = $this->createMock(ObjectRepository::class);
        $playersSkillsRepoMock->method('findBy')->willReturnOnConsecutiveCalls([$playerSkillMock]);

        $gameDataSkillRepoMock = $this->createMock(ObjectRepository::class);
        $gameDataSkillRepoMock->method('findOneBy')->willReturn($gameDataSkillMock0);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->will($this->returnCallback(
            function ($entityName) use ($playersSkillsRepoMock, $gameDataSkillRepoMock) {
                if ($entityName === GameDataSkills::class) {
                    return $gameDataSkillRepoMock;
                }

                if ($entityName === PlayersSkills::class) {
                    return $playersSkillsRepoMock;
                }

                return true;
            }
        ));

        $playerService = new PlayerService(
            $objectManager,
            $this->createMock(EquipeService::class),
            $this->createMock(MatchDataService::class),
            $this->createMock(InfosService::class)
        );

        $retourAttendu = '<text class="test-primary">Test</text>, <text class="text-danger">Test</text>, ';

        $this->assertEquals($retourAttendu, $playerService->toutesLesCompsdUnJoueur($joueurMock));
    }

    /**
     * @test
     */
    public function le_joueur_n_a_pas_de_comp()
    {
        $positionTest = new GameDataPlayers();
        $positionTest->setSkills(null);

        $joueurTest = new Players();
        $joueurTest->setJournalier(false);
        $joueurTest->setFPos($positionTest);

        $playersSkillsRepoMock = $this->createMock(ObjectRepository::class);
        $playersSkillsRepoMock->method('findBy')->willReturnOnConsecutiveCalls(false);

        $gameDataSkillRepoMock = $this->createMock(ObjectRepository::class);
        $gameDataSkillRepoMock->method('findOneBy')->willReturn(false);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->will($this->returnCallback(
            function ($entityName) use ($playersSkillsRepoMock, $gameDataSkillRepoMock) {
                if ($entityName === 'App\Entity\GameDataSkills') {
                    return $gameDataSkillRepoMock;
                }

                if ($entityName === 'App\Entity\PlayersSkills') {
                    return $playersSkillsRepoMock;
                }

                return true;
            }
        ));

        $playerService = new PlayerService(
            $objectManager,
            $this->createMock(EquipeService::class),
            $this->createMock(MatchDataService::class),
            $this->createMock(InfosService::class)
        );

        $this->assertEquals('', $playerService->toutesLesCompsdUnJoueur($joueurTest));
    }

}