<?php


namespace App\Tests\src\Service\InfosServices;


use App\Entity\Coaches;
use App\Entity\Races;
use App\Entity\RacesBb2020;
use App\Entity\Teams;
use App\Enum\RulesetEnum;
use App\Service\InfosService;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class equipeEstCreeTest extends TestCase
{
    /**
     * @test
     */
    public function une_equipe_est_cree_bb2016()
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
        $equipeMock->method('getRuleset')->willReturn(RulesetEnum::BB_2016);

        $envMock = $this->createMock(ContainerBagInterface::class);
        $envMock->method('get')->willReturn('dev');

        $infosServiceTest = new InfosService(
            $this->createMock(EntityManager::class),
            $envMock
        );

        $attendu = $infosServiceTest->equipeEstCree($equipeMock);

        $this->assertIsObject($attendu);
        $this->assertEquals('Zorro a crée l\'équipe <a href="/team/1">test</a>(Hobbit)', $attendu->getMessages());
    }

    /**
     * @test
     */
    public function une_equipe_est_cree_bb2020()
    {
        $coachMock = $this->createMock(Coaches::class);
        $coachMock->method('getName')->willReturn('Zorro');

        $raceMock = $this->createMock(RacesBb2020::class);
        $raceMock->method('getName')->willReturn('Hobbit');

        $equipeMock = $this->createMock(Teams::class);
        $equipeMock->method('getName')->willReturn('test');
        $equipeMock->method('getOwnedByCoach')->willReturn($coachMock);
        $equipeMock->method('getTeamId')->willReturn(1);
        $equipeMock->method('getRace')->willReturn($raceMock);
        $equipeMock->method('getRuleset')->willReturn(RulesetEnum::BB_2020);

        $envMock = $this->createMock(ContainerBagInterface::class);
        $envMock->method('get')->willReturn('dev');

        $infosServiceTest = new InfosService(
            $this->createMock(EntityManager::class),
            $envMock
        );

        $attentdu = $infosServiceTest->equipeEstCree($equipeMock);

        $this->assertIsObject($attentdu);
        $this->assertEquals('Zorro a crée l\'équipe <a href="/team/1">test</a>(Hobbit)', $attentdu->getMessages());
    }
}