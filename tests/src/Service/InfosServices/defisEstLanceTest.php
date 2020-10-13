<?php


namespace App\Tests\src\Service\InfosServices;

use App\Entity\Defis;
use App\Entity\Races;
use App\Entity\Teams;
use App\Service\InfosService;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;

class defisEstLanceTest extends TestCase
{
    /**
     * @test
     */
    public function le_message_defis_est_bien_fait()
    {
        $raceMock = $this->createMock(Races::class);
        $raceMock->method('getName')->willReturn('Hobbit');

        $equipeOrigineMock = $this->createMock(Teams::class);
        $equipeOrigineMock->method('getName')->willReturn('Equipe 1');
        $equipeOrigineMock->method('getTeamId')->willReturn(25);
        $equipeOrigineMock->method('getFRace')->willReturn($raceMock);

        $equipeDefieeMock = $this->createMock(Teams::class);
        $equipeDefieeMock->method('getName')->willReturn('Equipe 2');
        $equipeDefieeMock->method('getTeamId')->willReturn(26);
        $equipeDefieeMock->method('getFRace')->willReturn($raceMock);

        $defisMock = $this->createMock(Defis::class);
        $defisMock->method('getEquipeOrigine')->willReturn($equipeOrigineMock);
        $defisMock->method('getEquipeDefiee')->willReturn($equipeDefieeMock);

        $infosServiceTest = new InfosService(
            $this->createMock(EntityManager::class)
        );

        $attentdu = $infosServiceTest->defisEstLance($defisMock);

        $this->assertIsObject($attentdu);
        $this->assertEquals(
            '<a href="/team/25">Equipe 1</a> (Hobbit) défie <a href="/team/26">Equipe 2</a> (Hobbit)',
            $attentdu->getMessages()
        );
    }
}
