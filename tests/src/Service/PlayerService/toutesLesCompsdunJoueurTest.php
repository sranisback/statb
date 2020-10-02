<?php

namespace App\Tests\src\Service\PlayerService;


use App\Entity\GameDataPlayers;
use App\Entity\GameDataSkills;
use App\Entity\Players;
use App\Entity\PlayersSkills;
use App\Service\EquipeService;
use App\Service\MatchDataService;
use App\Service\PlayerService;
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
        $gameDataSkillTest0 = new GameDataSkills();
        $gameDataSkillTest0->setName('Test');

        $gameDataSkillTest1 = new GameDataSkills();
        $gameDataSkillTest1->setName('Test');

        $positionTest = new GameDataPlayers();
        $positionTest->setSkills('1');

        $joueurTest = new Players();
        $joueurTest->setType(1);
        $joueurTest->setFPos($positionTest);

        $playersSkillsTest = new PlayersSkills();
        $playersSkillsTest->setFSkill($gameDataSkillTest0);

        $playersSkillsRepoMock = $this->createMock(ObjectRepository::class);
        $playersSkillsRepoMock->method('findBy')->willReturnOnConsecutiveCalls([$playersSkillsTest]);

        $gameDataSkillRepoMock = $this->createMock(ObjectRepository::class);
        $gameDataSkillRepoMock->method('findOneBy')->willReturn($gameDataSkillTest1);

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
            $this->createMock(MatchDataService::class)
        );

        $retourAttendu = '<text class="test-primary">Test</text>, <text class="text-danger">Test</text>, ';

        $this->assertEquals($retourAttendu, $playerService->toutesLesCompsdUnJoueur($joueurTest));
    }

    /**
     * @test
     */
    public function le_joueur_n_a_pas_de_comp()
    {
        $positionTest = new GameDataPlayers();
        $positionTest->setSkills(null);

        $joueurTest = new Players();
        $joueurTest->setType(1);
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
            $this->createMock(MatchDataService::class)
        );

        $this->assertEquals('', $playerService->toutesLesCompsdUnJoueur($joueurTest));
    }

}