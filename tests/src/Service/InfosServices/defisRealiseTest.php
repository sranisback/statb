<?php


namespace App\Tests\src\Service\InfosServices;


use App\Entity\Defis;
use App\Entity\Matches;
use App\Entity\Teams;
use App\Enum\RulesetEnum;
use App\Service\InfosService;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;

class defisRealiseTest extends TestCase
{
    /**
     * @test
     */
    public function le_message_s_affiche_correctement_bb2016()
    {
        $equipeOrigineMock = $this->createMock(Teams::class);
        $equipeOrigineMock->method('getName')->willReturn('Equipe 1');
        $equipeOrigineMock->method('getTeamId')->willReturn(25);
        $equipeOrigineMock->method('getRuleset')->willReturn(RulesetEnum::BB_2016);

        $equipeDefieeMock = $this->createMock(Teams::class);
        $equipeDefieeMock->method('getName')->willReturn('Equipe 2');
        $equipeDefieeMock->method('getTeamId')->willReturn(26);
        $equipeDefieeMock->method('getRuleset')->willReturn(RulesetEnum::BB_2016);

        $matchmock = $this->createMock(Matches::class);
        $matchmock->method('getMatchId')->willReturn(55);

        $defisMock = $this->createMock(Defis::class);
        $defisMock->method('getEquipeOrigine')->willReturn($equipeOrigineMock);
        $defisMock->method('getEquipeDefiee')->willReturn($equipeDefieeMock);
        $defisMock->method('getMatchDefi')->willReturn($matchmock);

        $infosServiceTest = new InfosService(
            $this->createMock(EntityManager::class)
        );

        $attendu = $infosServiceTest->defisRealise($defisMock);

        $this->assertIsObject($attendu);
        $this->assertEquals(
            'Le défis <a href="/team/25">Equipe 1</a> contre <a href="/team/26">Equipe 2</a> a été réalisé : <a href="/match/55">Voir</a>',
            $attendu->getMessages()
        );
    }

    /**
     * @test
     */
    public function le_message_s_affiche_correctement_bb2020()
    {
        $equipeOrigineMock = $this->createMock(Teams::class);
        $equipeOrigineMock->method('getName')->willReturn('Equipe 1');
        $equipeOrigineMock->method('getTeamId')->willReturn(25);
        $equipeOrigineMock->method('getRuleset')->willReturn(RulesetEnum::BB_2020);

        $equipeDefieeMock = $this->createMock(Teams::class);
        $equipeDefieeMock->method('getName')->willReturn('Equipe 2');
        $equipeDefieeMock->method('getTeamId')->willReturn(26);
        $equipeDefieeMock->method('getRuleset')->willReturn(RulesetEnum::BB_2020);

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