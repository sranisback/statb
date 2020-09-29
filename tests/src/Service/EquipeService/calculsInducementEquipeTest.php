<?php


namespace App\Tests\src\Service\EquipeService;


use App\Entity\Races;
use App\Entity\Teams;
use App\Service\EquipeService;
use App\Service\PlayerService;
use App\Service\SettingsService;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;

class calculsInducementEquipeTest extends TestCase
{
    /**
     * @test
     */
    public function les_inducements_sont_calcules()
    {
        $raceMock = $this->createMock(Races::class);
        $raceMock->method('getCostRr')->willReturn(50000);

        $equipeMock = $this->createMock(Teams::class);
        $equipeMock->method('getFRace')->willReturn($raceMock);

        $playerServiceMock = $this->createMock(PlayerService::class);
        $playerServiceMock->method('coutTotalJoueurs')->willReturn(500000);

        $equipeServiceTest = new EquipeService(
            $this->createMock(EntityManager::class),
            $this->createMock(SettingsService::class)
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