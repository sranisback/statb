<?php

namespace App\Tests\src\Service\PlayerService;

use App\Entity\GameDataPlayers;
use App\Entity\GameDataSkills;
use App\Entity\MatchData;
use App\Entity\Players;
use App\Entity\Teams;
use App\Service\EquipeService;
use App\Service\MatchDataService;
use App\Service\PlayerService;
use Doctrine\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class renvoisOuSuppressionJoueurTest extends TestCase
{
    /**
     * @test
     */
    public function le_joueur_est_renvoye(): void
    {
        $positionTest = new GameDataPlayers();
        $positionTest->setCost(50_000);
        $positionTest->setSkills(50);

        $equipeTest = new Teams();
        $equipeTest->setTreasury(50_000);

        $playerTest = new Players();
        $playerTest->setFPos($positionTest);
        $playerTest->setOwnedByTeam($equipeTest);

        $matchDataTest = new MatchData();
        $matchDataTest->setFPlayer($playerTest);

        $matchDataRepoMock = $this->getMockBuilder(Players::class)
            ->addMethods(['listeDesMatchsdUnJoueur'])
            ->getMock();
        $matchDataRepoMock->method('listeDesMatchsdUnJoueur')->willReturn(
            [$matchDataTest]
        );

        $playersSkillsRepoMock = $this->createMock(ObjectRepository::class);
        $playersSkillsRepoMock->method('findBy')->willReturn([]);

        $gameDataSkillsTest = $this->createMock(GameDataSkills::class);
        $gameDataSkillsTest->method('getName')->willReturn('Disposable');
        $gameDataSkillsTest->method('getSkillId')->willReturn(1);

        $gameDataSkillRepoMock = $this->getMockBuilder(Players::class)
            ->setMethods(['findOneBy'])
            ->getMock();
        $gameDataSkillRepoMock->method('findOneBy')->willReturn($gameDataSkillsTest);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->will(
            $this->returnCallback(
                function ($entityName) use ($matchDataRepoMock, $playersSkillsRepoMock,$gameDataSkillRepoMock) {
                    if ($entityName === 'App\Entity\MatchData') {
                        return $matchDataRepoMock;
                    }

                    if ($entityName === 'App\Entity\PlayersSkills') {
                        return $playersSkillsRepoMock;
                    }

                    if ($entityName === 'App\Entity\GameDataSkills') {
                        return $gameDataSkillRepoMock;
                    }
                    return true;
                }
            )
        );

        $equipeServiceMock = $this->createMock(EquipeService::class);
        $equipeServiceMock->method('tvDelEquipe')->willReturnOnConsecutiveCalls(1_000_000);

        $playerServiceTest = new PlayerService(
            $objectManager,
            $equipeServiceMock,
            $this->createMock(MatchDataService::class)
        );

        $reponseTest = [
            'reponse' => 'sld',
            'tv' => 1_000,
            'tresor' => 50_000,
            'playercost' => 50_000,
        ];

        $this->assertEquals($reponseTest, $playerServiceTest->renvoisOuSuppressionJoueur($playerTest));
    }

    /**
     * @test
     */
    public function le_joueur_est_supprime(): void
    {
        $positionTest = new GameDataPlayers();
        $positionTest->setCost(50_000);
        $positionTest->setSkills(50);

        $equipeTest = new Teams();
        $equipeTest->setTreasury(50_000);

        $playerTest = new Players();
        $playerTest->setFPos($positionTest);
        $playerTest->setOwnedByTeam($equipeTest);
        $playerTest->setType(1);

        $matchDataRepoMock = $this->getMockBuilder(Players::class)
            ->setMethods(['listeDesMatchsdUnJoueur'])
            ->getMock();
        $matchDataRepoMock->method('listeDesMatchsdUnJoueur')->willReturn(
            []
        );

        $playersSkillsRepoMock = $this->createMock(ObjectRepository::class);
        $playersSkillsRepoMock->method('findBy')->willReturn([]);

        $gameDataSkillsTest = $this->createMock(GameDataSkills::class);
        $gameDataSkillsTest->method('getName')->willReturn('Disposable');
        $gameDataSkillsTest->method('getSkillId')->willReturn(1);

        $gameDataSkillRepoMock = $this->getMockBuilder(Players::class)
            ->setMethods(['findOneBy'])
            ->getMock();
        $gameDataSkillRepoMock->method('findOneBy')->willReturn($gameDataSkillsTest);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->will(
            $this->returnCallback(
                function ($entityName) use ($matchDataRepoMock, $playersSkillsRepoMock, $gameDataSkillRepoMock) {
                    if ($entityName === 'App\Entity\MatchData') {
                        return $matchDataRepoMock;
                    }

                    if ($entityName === 'App\Entity\PlayersSkills') {
                        return $playersSkillsRepoMock;
                    }

                    if ($entityName === 'App\Entity\GameDataSkills') {
                        return $gameDataSkillRepoMock;
                    }
                    return true;
                }
            )
        );

        $equipeServiceMock = $this->createMock(EquipeService::class);
        $equipeServiceMock->method('tvDelEquipe')->willReturn(1_000_000);

        $playerServiceTest = new PlayerService(
            $objectManager,
            $equipeServiceMock,
            $this->createMock(MatchDataService::class)
        );

        $reponseTest = [
            'reponse' => 'rm',
            'tv' => 1_000,
            'tresor' => 100_000,
            'playercost' => 50_000,
        ];

        $this->assertEquals($reponseTest, $playerServiceTest->renvoisOuSuppressionJoueur($playerTest));
    }
}