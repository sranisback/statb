<?php

namespace App\Tests\src\Service\EquipeGestionService;


use App\Entity\Coaches;
use App\Entity\GameDataPlayers;
use App\Entity\Players;
use App\Entity\PlayersIcons;
use App\Entity\Races;
use App\Entity\Teams;
use App\Service\PlayerService;
use App\Tests\src\TestServiceFactory\EquipeGestionServiceTestFactory;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;


class ajoutDesJournaliersTest extends TestCase
{
    /**
     * @test
     */
    public function un_journalier_est_ajoute_a_l_equipe(): void
    {
        $coachTest = new Coaches();

        $raceTest = new Races();
        $raceTest->setName('Halflings');

        $gameDataPlayerTest = new GameDataPlayers();
        $gameDataPlayerTest->setQty('16');
        $gameDataPlayerTest->setPos('halflings');
        $gameDataPlayerTest->setFRace($raceTest);
        $gameDataPlayerTest->setCost(50_000);

        $equipeTest = new Teams();
        $equipeTest->setFRace($raceTest);
        $equipeTest->setOwnedByCoach($coachTest);

        $joueurTest0 = new Players();
        $joueurTest0->setOwnedByTeam($equipeTest);
        $joueurTest0->setNr(1);

        $joueurTest1 = new Players();
        $joueurTest1->setOwnedByTeam($equipeTest);
        $joueurTest1->setNr(2);

        $joueurTest2 = new Players();
        $joueurTest2->setOwnedByTeam($equipeTest);
        $joueurTest2->setNr(3);

        $joueurTest3 = new Players();
        $joueurTest3->setOwnedByTeam($equipeTest);
        $joueurTest3->setNr(4);

        $joueurTest4 = new Players();
        $joueurTest4->setOwnedByTeam($equipeTest);
        $joueurTest4->setNr(5);

        $joueurTest5 = new Players();
        $joueurTest5->setOwnedByTeam($equipeTest);
        $joueurTest5->setNr(6);

        $joueurTest6 = new Players();
        $joueurTest6->setOwnedByTeam($equipeTest);
        $joueurTest6->setNr(7);

        $joueurTest7 = new Players();
        $joueurTest7->setOwnedByTeam($equipeTest);
        $joueurTest7->setNr(8);

        $joueurTest8 = new Players();
        $joueurTest8->setOwnedByTeam($equipeTest);
        $joueurTest8->setNr(9);

        $joueurTest9 = new Players();
        $joueurTest9->setOwnedByTeam($equipeTest);
        $joueurTest9->setNr(10);

        $gameDataPlayersRepoMock = $this->createMock(ObjectRepository::class);
        $gameDataPlayersRepoMock->method('findOneBy')->willReturn($gameDataPlayerTest);

        $joueurRepoMock = $this->getMockBuilder(Players::class)
            ->addMethods(['listeDesJoueursActifsPourlEquipe'])
            ->getMock();
        $joueurRepoMock->method('listeDesJoueursActifsPourlEquipe')->willReturn(
            [
                $joueurTest0,
                $joueurTest1,
                $joueurTest2,
                $joueurTest3,
                $joueurTest4,
                $joueurTest5,
                $joueurTest6,
                $joueurTest7,
                $joueurTest8,
                $joueurTest9,
            ]
        );

        $playerIconRepoMock = $this->getMockBuilder(PlayersIcons::class)
            ->addMethods(['toutesLesIconesDunePosition'])
            ->getMock();
        $playerIconRepoMock->method('toutesLesIconesDunePosition')
            ->willReturn([$this->createMock(PlayersIcons::class)]);

        $objectManager = $this->createMock(EntityManager::class);
        $objectManager->method('getRepository')->will($this->returnCallback(
            function ($entityName) use ($gameDataPlayersRepoMock, $joueurRepoMock, $playerIconRepoMock) {
                if ($entityName === 'App\Entity\GameDataPlayers') {
                    return $gameDataPlayersRepoMock;
                }

                if ($entityName === 'App\Entity\Players') {
                    return $joueurRepoMock;
                }

                if ($entityName === 'App\Entity\PlayersIcons') {
                    return $playerIconRepoMock;
                }
                return true;
            }
        ));

        $playerServiceMock = $this->createMock(PlayerService::class);
        $playerServiceMock->method('numeroLibreDelEquipe')->willReturn(11);

        $equipeGestionService = (new EquipeGestionServiceTestFactory())->getInstance(
            $objectManager
        );

        $this->assertEquals(1, $equipeGestionService->ajoutDesJournaliers(1, $equipeTest, $playerServiceMock));
    }

    /**
     * @test
     */
    public function il_n_y_a_aucun_joueur(): void
    {
        $coachTest = new Coaches();

        $raceTest = new Races();
        $raceTest->setName('Halflings');

        $gameDataPlayerTest = new GameDataPlayers();
        $gameDataPlayerTest->setQty('16');
        $gameDataPlayerTest->setPos('halflings');
        $gameDataPlayerTest->setFRace($raceTest);
        $gameDataPlayerTest->setCost(50_000);

        $equipeTest = new Teams();
        $equipeTest->setFRace($raceTest);
        $equipeTest->setOwnedByCoach($coachTest);

        $gameDataPlayersRepoMock = $this->createMock(ObjectRepository::class);
        $gameDataPlayersRepoMock->method('findOneBy')->willReturn($gameDataPlayerTest);

        $playerIconRepoMock = $this->getMockBuilder(PlayersIcons::class)
            ->addMethods(['toutesLesIconesDunePosition'])
            ->getMock();
        $playerIconRepoMock->method('toutesLesIconesDunePosition')
            ->willReturn([$this->createMock(PlayersIcons::class)]);

        $joueurRepoMock = $this->getMockBuilder(Players::class)
            ->addMethods(['listeDesJoueursActifsPourlEquipe'])
            ->getMock();
        $joueurRepoMock->method('listeDesJoueursActifsPourlEquipe')->willReturn(
            []
        );

        $objectManager = $this->createMock(EntityManager::class);
        $objectManager->method('getRepository')->will($this->returnCallback(
            function ($entityName) use ($gameDataPlayersRepoMock, $joueurRepoMock, $playerIconRepoMock) {
                if ($entityName === 'App\Entity\GameDataPlayers') {
                    return $gameDataPlayersRepoMock;
                }

                if ($entityName === 'App\Entity\Players') {
                    return $joueurRepoMock;
                }

                if ($entityName === 'App\Entity\PlayersIcons') {
                    return $playerIconRepoMock;
                }
                return true;
            }
        ));

        $playerServiceMock = $this->createMock(PlayerService::class);
        $playerServiceMock->method('numeroLibreDelEquipe')->willReturn(11);

        $equipeGestionService = (new EquipeGestionServiceTestFactory())->getInstance(
            $objectManager
        );

        $this->assertEquals(11, $equipeGestionService->ajoutDesJournaliers(11, $equipeTest, $playerServiceMock));
    }
}