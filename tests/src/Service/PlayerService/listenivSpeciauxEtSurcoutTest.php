<?php

namespace App\Tests\src\Service\PlayerService;

use App\Entity\Players;
use App\Enum\RulesetEnum;
use App\Service\MatchDataService;
use App\Tests\src\TestServiceFactory\PlayerServiceTestFactory;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class listenivSpeciauxEtSurcoutTest extends TestCase
{
    /**
     * @test
     */
    public function tous_les_niv_spec_sont_retournes_bb2016(): void
    {
        $joueurMock = $this->createMock(Players::class);
        $joueurMock->method('getAchSt')->willReturn(1);
        $joueurMock->method('getRuleset')->willReturn(RulesetEnum::BB_2016);

        $objectManager = $this->createMock(EntityManagerInterface::class);

        $matchDataService = new MatchDataService($objectManager);

        $playerService = (new PlayerServiceTestFactory())->getInstance(
            $objectManager,
            $matchDataService
        );

        $retour = ['nivspec' => '<text class="text-success">+1 St</text>, ', 'cout' => 50_000];

        $this->assertEquals($retour, $playerService->listenivSpeciauxEtSurcout($joueurMock));
    }

    /**
     * @test
     */
    public function tous_les_niv_spec_sont_retournes_bb2020(): void
    {
        $joueurMock = $this->createMock(Players::class);
        $joueurMock->method('getAchSt')->willReturn(1);
        $joueurMock->method('getRuleset')->willReturn(RulesetEnum::BB_2020);

        $objectManager = $this->createMock(EntityManagerInterface::class);

        $matchDataService = new MatchDataService($objectManager);

        $playerService = (new PlayerServiceTestFactory())->getInstance(
            $objectManager,
            $matchDataService
        );

        $retour = ['nivspec' => '<text class="text-success">+1 St</text>, ', 'cout' => 80_000];

        $this->assertEquals($retour, $playerService->listenivSpeciauxEtSurcout($joueurMock));
    }
}