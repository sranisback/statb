<?php

namespace App\Tests\src\Service\PlayerService;

use App\Entity\MatchData;
use App\Entity\Matches;
use App\Entity\Players;
use App\Service\EquipeService;
use App\Service\MatchDataService;
use App\Service\PlayerService;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class actionDuJoueurDansUnMatchTest extends KernelTestCase
{
    /**
     * @test
     */
    public function les_actions_du_joueur_pour_un_match_sont_bien_retournees()
    {
        $matchDataTest0 = new MatchData();
        $matchDataTest0->setMvp(1);
        $matchDataTest1 = new MatchData();
        $matchDataTest1->setTd(1);
        $matchDataMock2 = new MatchData();

        $matchDataRepoMock = $this->createMock(ObjectRepository::class);
        $matchDataRepoMock->method('findBy')->willReturn([$matchDataTest0, $matchDataTest1, $matchDataMock2]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchDataRepoMock);

        $matchDataService = new MatchDataService($objectManager);

        $playerService = new PlayerService(
            $objectManager,
            $this->createMock(EquipeService::class),
            $matchDataService
        );
        $this->assertEquals(
            'MVP: 1, TD: 1, ',
            $playerService->actionDuJoueurDansUnMatch(
                $this->createMock(Matches::class),
                $this->createMock(Players::class)
            )
        );
    }

    /**
     * @test
     */
    public function il_n_y_a_pas_de_donnees()
    {
        $matchDataTest0 = new MatchData();
        $matchDataTest1 = new MatchData();
        $matchDataMock2 = new MatchData();

        $matchDataRepoMock = $this->createMock(ObjectRepository::class);
        $matchDataRepoMock->method('findBy')->willReturn([$matchDataTest0, $matchDataTest1, $matchDataMock2]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchDataRepoMock);

        $matchDataService = new MatchDataService($objectManager);

        $playerService = new PlayerService(
            $objectManager,
            $this->createMock(EquipeService::class),
            $matchDataService
        );
        $this->assertEquals(
            '',
            $playerService->actionDuJoueurDansUnMatch(
                $this->createMock(Matches::class),
                $this->createMock(Players::class)
            )
        );
    }
}