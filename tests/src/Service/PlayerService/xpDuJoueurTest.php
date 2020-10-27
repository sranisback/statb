<?php

namespace App\Tests\src\Service\PlayerService;

use App\Entity\MatchData;
use App\Entity\Players;
use App\Service\EquipeService;
use App\Service\InfosService;
use App\Service\MatchDataService;
use App\Service\PlayerService;
use Doctrine\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class xpDuJoueurTest extends KernelTestCase
{
    /**
     * @test
     */
    public function l_xp_totale_est_bien_calculee(): void
    {
        $joueurTest = new Players();

        $matchDataTest0 = new MatchData();
        $matchDataTest0->setMvp(1);
        $matchDataTest0->setCp(1);
        $matchDataTest0->setBh(1);
        $matchDataTest1 = new MatchData();
        $matchDataTest1->setTd(1);
        $matchDataTest1->setIntcpt(1);

        $matchDataRepoMock = $this->createMock(ObjectRepository::class);
        $matchDataRepoMock->method('findBy')->willReturn([$matchDataTest0, $matchDataTest1]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchDataRepoMock);

        $playerServiceTest = new PlayerService(
            $objectManager,
            $this->createMock(EquipeService::class),
            $this->createMock(MatchDataService::class),
            $this->createMock(InfosService::class)
        );

        $this->assertEquals(13, $playerServiceTest->xpDuJoueur($joueurTest));
    }

    /**
     * @test
     */
    public function il_n_y_a_pas_de_donnees(): void
    {
        $joueurTest = new Players();

        $matchDataRepoMock = $this->createMock(ObjectRepository::class);
        $matchDataRepoMock->method('findBy')->willReturn([]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchDataRepoMock);

        $playerServiceTest = new PlayerService(
            $objectManager,
            $this->createMock(EquipeService::class),
            $this->createMock(MatchDataService::class),
            $this->createMock(InfosService::class)
        );

        $this->assertEquals(0, $playerServiceTest->xpDuJoueur($joueurTest));
    }


    /**
     * @test
     */
    public function le_joueur_n_a_rien_fait_dans_les_matches(): void
    {
        $joueurTest = new Players();

        $matchDataTest0 = new MatchData();
        $matchDataTest1 = new MatchData();

        $matchDataRepoMock = $this->createMock(ObjectRepository::class);
        $matchDataRepoMock->method('findBy')->willReturn([$matchDataTest0, $matchDataTest1]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchDataRepoMock);

        $playerServiceTest = new PlayerService(
            $objectManager,
            $this->createMock(EquipeService::class),
            $this->createMock(MatchDataService::class),
            $this->createMock(InfosService::class)
        );

        $this->assertEquals(0, $playerServiceTest->xpDuJoueur($joueurTest));
    }
}