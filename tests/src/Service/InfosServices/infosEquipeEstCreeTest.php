<?php


namespace App\Tests\src\Service\InfosServices;


use App\Entity\Coaches;
use App\Entity\Races;
use App\Entity\Teams;
use App\Service\InfosService;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;

class infosEquipeEstCreeTest extends TestCase
{
    /**
     * @test
     */
    public function une_equipe_est_cree()
    {
        $coachMock = $this->createMock(Coaches::class);
        $coachMock->method('getName')->willReturn('Zorro');

        $raceMock = $this->createMock(Races::class);
        $raceMock->method('getName')->willReturn('Hobbit');

        $equipeMock = $this->createMock(Teams::class);
        $equipeMock->method('getName')->willReturn('test');
        $equipeMock->method('getOwnedByCoach')->willReturn($coachMock);
        $equipeMock->method('getTeamId')->willReturn(1);
        $equipeMock->method('getFRace')->willReturn($raceMock);

        $infosServiceTest = new InfosService(
            $this->createMock(EntityManager::class)
        );

        $attentdu = $infosServiceTest->infosEquipeEstCree($equipeMock);

        $this->assertIsObject($attentdu);
        $this->assertEquals('Zorro a crée l\'équipe <a href="/team/1">test</a>(Hobbit)', $attentdu->getMessages());
    }
}