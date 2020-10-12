<?php


namespace App\Tests\src\Service\InfosServices;


use App\Entity\Defis;
use App\Entity\Matches;
use App\Entity\Teams;
use App\Service\InfosService;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;

class defisRealiseTest extends TestCase
{
    /**
     * @test
     */
    public function le_message_s_affiche_correctement()
    {
        $equipeOrigineMock = $this->createMock(Teams::class);
        $equipeOrigineMock->method('getName')->willReturn('Equipe 1');
        $equipeOrigineMock->method('getTeamId')->willReturn(25);

        $equipeDefieeMock = $this->createMock(Teams::class);
        $equipeDefieeMock->method('getName')->willReturn('Equipe 2');
        $equipeDefieeMock->method('getTeamId')->willReturn(26);

        $matchmock = $this->createMock(Matches::class);
        $matchmock->method('getMatchId')->willReturn(55);

        $defisMock = $this->createMock(Defis::class);
        $defisMock->method('getEquipeOrigine')->willReturn($equipeOrigineMock);
        $defisMock->method('getEquipeDefiee')->willReturn($equipeDefieeMock);
        $defisMock->method('getMatchDefi')->willReturn($matchmock);

        $infosServiceTest = new InfosService(
            $this->createMock(EntityManager::class)
        );

        $attentdu = $infosServiceTest->defisRealise($defisMock);

        $this->assertIsObject($attentdu);
        $this->assertEquals(
            'Le défis <a href="/team/25">Equipe 1</a> contre <a href="/team/26">Equipe 2</a> a été réalisé : <a href="/match/55">Voir</a>',
            $attentdu->getMessages()
        );
    }

}