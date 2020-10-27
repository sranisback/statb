<?php

namespace App\Tests\src\Service\PlayerService;

use App\Entity\Players;
use App\Service\EquipeService;
use App\Service\InfosService;
use App\Service\MatchDataService;
use App\Service\PlayerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class listenivSpeciauxEtSurcoutTest extends KernelTestCase
{
    /**
     * @test
     */
    public function tous_les_niv_spec_sont_retournes(): void
    {
        $joueurMock = $this->createMock(Players::class);
        $joueurMock->method('getAchSt')->willReturn(1);

        $objectManager = $this->createMock(EntityManagerInterface::class);

        $matchDataService = new MatchDataService($objectManager);

        $playerService = new PlayerService(
            $objectManager,
            $this->createMock(EquipeService::class),
            $matchDataService,
            $this->createMock(InfosService::class)
        );

        $retour = ['nivspec' => '<text class="text-success">+1 St</text>, ', 'cout' => 50_000];

        $this->assertEquals($retour, $playerService->listenivSpeciauxEtSurcout($joueurMock));
    }
}