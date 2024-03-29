<?php

namespace App\Tests\src\Service\PlayerService;

use App\Entity\MatchData;
use App\Entity\Matches;
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

class actionDuJoueurDansUnMatchTest extends TestCase
{
    /**
     * @test
     */
    public function les_actions_du_joueur_pour_un_match_sont_bien_retournees(): void
    {
        $matchDataTest0 = new MatchData();
        $matchDataTest0->setMvp(1);
        $matchDataTest1 = new MatchData();
        $matchDataTest1->setTd(1);
        $matchDataMock2 = new MatchData();

        $matchMock = $this->createMock(Matches::class);
        $matchMock->method('getBlessuresMatch')->willReturn(new ArrayCollection());

        $matchDataRepoMock = $this->createMock(ObjectRepository::class);
        $matchDataRepoMock->method('findBy')->willReturn([$matchDataTest0, $matchDataTest1, $matchDataMock2]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchDataRepoMock);

        $matchDataService = new MatchDataService($objectManager);

        $playerService = new PlayerService(
            $objectManager,
            $this->createMock(EquipeGestionService::class),
            $matchDataService,
            $this->createMock(InfosService::class)
        );
        $this->assertEquals(
            'MVP: 1, TD: 1, ',
            $playerService->actionDuJoueurDansUnMatch(
                $matchMock,
                $this->createMock(Players::class)
            )
        );
    }

    /**
     * @test
     */
    public function il_n_y_a_pas_de_donnees(): void
    {
        $matchDataTest0 = new MatchData();
        $matchDataTest1 = new MatchData();
        $matchDataMock2 = new MatchData();

        $matchMock = $this->createMock(Matches::class);
        $matchMock->method('getBlessuresMatch')->willReturn(new ArrayCollection());

        $matchDataRepoMock = $this->createMock(ObjectRepository::class);
        $matchDataRepoMock->method('findBy')->willReturn([$matchDataTest0, $matchDataTest1, $matchDataMock2]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchDataRepoMock);

        $matchDataService = new MatchDataService($objectManager);

        $playerService = new PlayerService(
            $objectManager,
            $this->createMock(EquipeGestionService::class),
            $matchDataService,
            $this->createMock(InfosService::class)
        );
        $this->assertEquals(
            '',
            $playerService->actionDuJoueurDansUnMatch(
                $matchMock,
                $this->createMock(Players::class)
            )
        );
    }
}