<?php

namespace App\Tests\src\Service\PlayerService;

use App\Entity\GameDataPlayers;
use App\Entity\MatchData;
use App\Entity\Players;
use App\Entity\Teams;
use App\Service\EquipeService;
use App\Service\MatchDataService;
use App\Service\PlayerService;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class renvoisOuSuppressionJoueurTest extends KernelTestCase
{
    /**
     * @test
     */
    public function le_joueur_est_renvoye()
    {
        $positionTest = new GameDataPlayers();
        $positionTest->setCost(50000);

        $equipeTest = new Teams();
        $equipeTest->setTreasury(50000);

        $playerTest = new Players();
        $playerTest->setFPos($positionTest);
        $playerTest->setOwnedByTeam($equipeTest);

        $matchDataTest = new MatchData();
        $matchDataTest->setFPlayer($playerTest);

        $matchDataRepoMock = $this->getMockBuilder(Players::class)
            ->setMethods(['listeDesMatchsdUnJoueur'])
            ->getMock();
        $matchDataRepoMock->method('listeDesMatchsdUnJoueur')->willReturn(
            [$matchDataTest]
        );

        $playersSkillsRepoMock = $this->createMock(ObjectRepository::class);
        $playersSkillsRepoMock->method('findBy')->willReturn([]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->will(
            $this->returnCallback(
                function ($entityName) use ($matchDataRepoMock, $playersSkillsRepoMock) {
                    if ($entityName === 'App\Entity\MatchData') {
                        return $matchDataRepoMock;
                    }

                    if ($entityName === 'App\Entity\PlayersSkills') {
                        return $playersSkillsRepoMock;
                    }

                    return true;
                }
            )
        );

        $equipeServiceMock = $this->createMock(EquipeService::class);
        $equipeServiceMock->method('tvDelEquipe')->willReturn(1000);

        $playerServiceTest = new PlayerService(
            $objectManager,
            $equipeServiceMock,
            $this->createMock(MatchDataService::class)
        );

        $reponseTest = [
            'reponse' => 'sld',
            'tv' => 1000,
            'tresor' => 50000,
            'playercost' => 50000,
        ];

        $this->assertEquals($reponseTest, $playerServiceTest->renvoisOuSuppressionJoueur($playerTest));
    }

    /**
     * @test
     */
    public function le_joueur_est_supprime()
    {
        $positionTest = new GameDataPlayers();
        $positionTest->setCost(50000);

        $equipeTest = new Teams();
        $equipeTest->setTreasury(50000);

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
        $playersSkillsRepoMock->method('findBy')->willReturn(null);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->will(
            $this->returnCallback(
                function ($entityName) use ($matchDataRepoMock, $playersSkillsRepoMock) {
                    if ($entityName === 'App\Entity\MatchData') {
                        return $matchDataRepoMock;
                    }

                    if ($entityName === 'App\Entity\PlayersSkills') {
                        return $playersSkillsRepoMock;
                    }

                    return true;
                }
            )
        );

        $equipeServiceMock = $this->createMock(EquipeService::class);
        $equipeServiceMock->method('tvDelEquipe')->willReturn(1000);

        $playerServiceTest = new PlayerService(
            $objectManager,
            $equipeServiceMock,
            $this->createMock(MatchDataService::class)
        );

        $reponseTest = [
            'reponse' => 'rm',
            'tv' => 1000,
            'tresor' => 100000,
            'playercost' => 50000,
        ];

        $this->assertEquals($reponseTest, $playerServiceTest->renvoisOuSuppressionJoueur($playerTest));
    }
}