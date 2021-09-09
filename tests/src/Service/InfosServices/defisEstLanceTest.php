<?php


namespace App\Tests\src\Service\InfosServices;

use App\Entity\Defis;
use App\Entity\Races;
use App\Entity\RacesBb2020;
use App\Entity\Teams;
use App\Enum\RulesetEnum;
use App\Service\InfosService;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class defisEstLanceTest extends TestCase
{
    /**
     * @test
     */
    public function le_message_defis_est_bien_fait_bb2016()
    {
        $raceMock = $this->createMock(Races::class);
        $raceMock->method('getName')->willReturn('Hobbit');

        $equipeOrigineMock = $this->createMock(Teams::class);
        $equipeOrigineMock->method('getName')->willReturn('Equipe 1');
        $equipeOrigineMock->method('getTeamId')->willReturn(25);
        $equipeOrigineMock->method('getFRace')->willReturn($raceMock);
        $equipeOrigineMock->method('getRuleset')->willReturn(RulesetEnum::BB_2016);

        $equipeDefieeMock = $this->createMock(Teams::class);
        $equipeDefieeMock->method('getName')->willReturn('Equipe 2');
        $equipeDefieeMock->method('getTeamId')->willReturn(26);
        $equipeDefieeMock->method('getFRace')->willReturn($raceMock);
        $equipeOrigineMock->method('getRuleset')->willReturn(RulesetEnum::BB_2016);

        $defisMock = $this->createMock(Defis::class);
        $defisMock->method('getEquipeOrigine')->willReturn($equipeOrigineMock);
        $defisMock->method('getEquipeDefiee')->willReturn($equipeDefieeMock);

        $envMock = $this->createMock(ContainerBagInterface::class);
        $envMock->method('get')->willReturn('dev');

        $infosServiceTest = new InfosService(
            $this->createMock(EntityManager::class),
            $envMock
        );

        $attendu = $infosServiceTest->defisEstLance($defisMock);

        $this->assertIsObject($attendu);
        $this->assertEquals(
            '<a href="/team/25">Equipe 1</a> (Hobbit) défie <a href="/team/26">Equipe 2</a> (Hobbit)',
            $attendu->getMessages()
        );
    }

    /**
     * @test
     */
    public function le_message_defis_est_bien_fait_bb2020()
    {
        $raceMock = $this->createMock(RacesBb2020::class);
        $raceMock->method('getName')->willReturn('Hobbit');

        $equipeOrigineMock = $this->createMock(Teams::class);
        $equipeOrigineMock->method('getName')->willReturn('Equipe 1');
        $equipeOrigineMock->method('getTeamId')->willReturn(25);
        $equipeOrigineMock->method('getRace')->willReturn($raceMock);
        $equipeOrigineMock->method('getRuleset')->willReturn(RulesetEnum::BB_2020);

        $equipeDefieeMock = $this->createMock(Teams::class);
        $equipeDefieeMock->method('getName')->willReturn('Equipe 2');
        $equipeDefieeMock->method('getTeamId')->willReturn(26);
        $equipeDefieeMock->method('getRace')->willReturn($raceMock);
        $equipeDefieeMock->method('getRuleset')->willReturn(RulesetEnum::BB_2020);

        $defisMock = $this->createMock(Defis::class);
        $defisMock->method('getEquipeOrigine')->willReturn($equipeOrigineMock);
        $defisMock->method('getEquipeDefiee')->willReturn($equipeDefieeMock);

        $envMock = $this->createMock(ContainerBagInterface::class);
        $envMock->method('get')->willReturn('dev');

        $infosServiceTest = new InfosService(
            $this->createMock(EntityManager::class),
            $envMock
        );

        $attendu = $infosServiceTest->defisEstLance($defisMock);

        $this->assertIsObject($attendu);
        $this->assertEquals(
            '<a href="/team/25">Equipe 1</a> (Hobbit) défie <a href="/team/26">Equipe 2</a> (Hobbit)',
            $attendu->getMessages()
        );
    }
}
