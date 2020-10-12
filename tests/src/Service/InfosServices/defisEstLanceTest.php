<?php


namespace App\Tests\src\Service\InfosServices;


use App\Entity\Defis;
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
        $equipeOrigineMock = $this->createMock(Teams::class);
        $equipeOrigineMock->method('getName')->willReturn('Equipe 1');
        $equipeOrigineMock->method('getTeamId')->willReturn(25);

        $equipeDefieeMock = $this->createMock(Teams::class);
        $equipeDefieeMock->method('getName')->willReturn('Equipe 2');
        $equipeDefieeMock->method('getTeamId')->willReturn(26);

        $defisMock = $this->createMock(Defis::class);
        $defisMock->method('getEquipeOrigine')->willReturn($equipeOrigineMock);
        $defisMock->method('getEquipeDefiee')->willReturn($equipeDefieeMock);

        $infosServiceTest = new InfosService(
            $this->createMock(EntityManager::class)
        );

        $attentdu = $infosServiceTest->defisEstLance($defisMock);

        $this->assertIsObject($attentdu);
        $this->assertEquals('<a href="/team/25">Equipe 1</a> défie <a href="/team/26">Equipe 2</a>', $attentdu->getMessages());
    }
}