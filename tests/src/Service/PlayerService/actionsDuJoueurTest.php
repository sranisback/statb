<?php
namespace App\Tests\src\Service\PlayerService;

use App\Entity\MatchData;
use App\Entity\Players;
use App\Service\EquipeService;
use App\Service\MatchDataService;
use App\Service\PlayerService;
use Doctrine\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class actionsDuJoueurTest extends TestCase
{
    /**
     * @test
     */
    public function les_actions_sont_retournees_correctement(): void
    {
        $retour = [
            'NbrMatch' => 1,
            'cp' => 0,
            'td' => 0,
            'int' => 0,
            'cas' => 0,
            'mvp' => 1,
            'agg' => 0,
        ];

        $playerMock = $this->createMock(Players::class);
        $playerMock->method('getPlayerId')->willReturn(0);

        $matchDataMock0 = $this->createMock(MatchData::class);
        $matchDataMock0->method('getMvp')->willReturn(1);

        $matchDataRepoMock = $this->createMock(ObjectRepository::class);
        $matchDataRepoMock->method('findBy')->willReturn([$matchDataMock0]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchDataRepoMock);

        $matchDataService = new MatchDataService($objectManager);

        $playerService = new PlayerService(
            $objectManager,
            $this->createMock(EquipeService::class),
            $matchDataService
        );

        $this->assertEquals($retour, $playerService->actionsDuJoueur($playerMock));
    }
}