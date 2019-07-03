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
        $matchDataMock0 = $this->createMock(MatchData::class);
        $matchDataMock0->method('getMvp')->willReturn(1);
        $matchDataMock1 = $this->createMock(MatchData::class);
        $matchDataMock0->method('getTd')->willReturn(1);
        $matchDataMock2 = $this->createMock(MatchData::class);

        $matchDataRepoMock = $this->createMock(ObjectRepository::class);
        $matchDataRepoMock->method('findBy')->willReturn([$matchDataMock0, $matchDataMock1, $matchDataMock2]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchDataRepoMock);

        $matchDataService = new MatchDataService($objectManager);

        $playerService = new PlayerService(
            $objectManager,
            $this->createMock(EquipeService::class),
            $matchDataService
        );
        $this->assertEquals(
            'TD: 1, MVP: 1, ',
            $playerService->actionDuJoueurDansUnMatch(
                $this->createMock(Matches::class),
                $this->createMock(Players::class)
            )
        );
    }
}