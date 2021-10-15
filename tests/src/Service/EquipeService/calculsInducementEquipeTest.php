<?php


namespace App\Tests\src\Service\EquipeService;


use App\Entity\Races;
use App\Entity\RacesBb2020;
use App\Entity\Teams;
use App\Enum\RulesetEnum;
use App\Service\EquipeService;
use App\Service\InducementService;
use App\Service\InfosService;
use App\Service\PlayerService;
use App\Service\SettingsService;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;

class calculsInducementEquipeTest extends TestCase
{
    /**
     * @test
     */
    public function les_inducements_sont_calcules_bb2016()
    {
        $raceMock = $this->createMock(Races::class);
        $raceMock->method('getCostRr')->willReturn(50000);

        $equipeMock = $this->createMock(Teams::class);
        $equipeMock->method('getFRace')->willReturn($raceMock);
        $equipeMock->method('getRuleset')->willReturn(RulesetEnum::BB_2016);

        $playerServiceMock = $this->createMock(PlayerService::class);
        $playerServiceMock->method('coutTotalJoueurs')->willReturn(500000);

        $equipeServiceTest = new EquipeService(
            $this->createMock(EntityManager::class),
            $this->createMock(SettingsService::class),
            $this->createMock(InfosService::class),
            $this->createMock(InducementService::class)
        );

        $attendu = [
            'playersCost' => 500000,
            'rerolls' => 0,
            'pop' => 0,
            'asscoaches' => 0,
            'cheerleader' => 0,
            'apo' => 0,
            'tv' => 500000
        ];

        $this->assertEquals($attendu, $equipeServiceTest->calculsInducementEquipe($equipeMock,$playerServiceMock));
    }

    /**
     * @test
     */
    public function les_inducements_sont_calcules_bb2020()
    {
        $raceMock = $this->createMock(RacesBb2020::class);
        $raceMock->method('getCostRr')->willReturn(50000);

        $equipeMock = $this->createMock(Teams::class);
        $equipeMock->method('getRace')->willReturn($raceMock);
        $equipeMock->method('getRuleset')->willReturn(RulesetEnum::BB_2020);

        $playerServiceMock = $this->createMock(PlayerService::class);
        $playerServiceMock->method('coutTotalJoueurs')->willReturn(500000);

        $equipeServiceTest = new EquipeService(
            $this->createMock(EntityManager::class),
            $this->createMock(SettingsService::class),
            $this->createMock(InfosService::class),
            $this->createMock(InducementService::class)
        );

        $attendu = [
            'playersCost' => 500000,
            'rerolls' => 0,
            'pop' => 0,
            'asscoaches' => 0,
            'cheerleader' => 0,
            'apo' => 0,
            'tv' => 500000
        ];

        $this->assertEquals($attendu, $equipeServiceTest->calculsInducementEquipe($equipeMock,$playerServiceMock));
    }
}