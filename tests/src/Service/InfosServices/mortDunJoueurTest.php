<?php


namespace App\Tests\src\Service\InfosServices;


use App\Entity\GameDataPlayers;
use App\Entity\GameDataPlayersBb2020;
use App\Entity\Players;
use App\Entity\Races;
use App\Entity\RacesBb2020;
use App\Entity\Teams;
use App\Enum\RulesetEnum;
use App\Service\InfosService;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;

class mortDunJoueurTest extends TestCase
{
    /**
     * @test
     */
    public function le_message_est_bien_forme_bb2016()
    {
        $raceMock = $this->createMock(Races::class);
        $raceMock->method('getName')->willReturn('Hobbit');

        $gameDataPositionMock = $this->createMock(GameDataPlayers::class);
        $gameDataPositionMock->method('getPos')->willReturn('Coupeur de citron');

        $equipeMock = $this->createMock(Teams::class);
        $equipeMock->method('getName')->willReturn('Totomen');
        $equipeMock->method('getTeamId')->willReturn(25);

        $joueurMock = $this->createMock(Players::class);
        $joueurMock->method('getName')->willReturn('Toto');
        $joueurMock->method('getFRid')->willReturn($raceMock);
        $joueurMock->method('getFPos')->willReturn($gameDataPositionMock);
        $joueurMock->method('getOwnedByTeam')->willReturn($equipeMock);
        $joueurMock->method('getRuleset')->willReturn(RulesetEnum::BB_2016);

        $infosServiceTest = new InfosService(
            $this->createMock(EntityManager::class)
        );

        $attentdu = $infosServiceTest->mortDunJoueur($joueurMock);

        $this->assertIsObject($attentdu);
        $this->assertEquals(
            'Toto, Coupeur de citron Hobbit de <a href="/team/25">Totomen</a> est mort !',
            $attentdu->getMessages()
        );
    }

    /**
     * @test
     */
    public function le_message_est_bien_forme_bb2020()
    {
        $raceMock = $this->createMock(RacesBb2020::class);
        $raceMock->method('getName')->willReturn('Hobbit');

        $gameDataPositionMock = $this->createMock(GameDataPlayersBb2020::class);
        $gameDataPositionMock->method('getPos')->willReturn('Coupeur de citron');

        $equipeMock = $this->createMock(Teams::class);
        $equipeMock->method('getName')->willReturn('Totomen');
        $equipeMock->method('getTeamId')->willReturn(25);

        $joueurMock = $this->createMock(Players::class);
        $joueurMock->method('getName')->willReturn('Toto');
        $joueurMock->method('getFRidBb2020')->willReturn($raceMock);
        $joueurMock->method('getFPosBb2020')->willReturn($gameDataPositionMock);
        $joueurMock->method('getOwnedByTeam')->willReturn($equipeMock);
        $joueurMock->method('getRuleset')->willReturn(RulesetEnum::BB_2020);

        $infosServiceTest = new InfosService(
            $this->createMock(EntityManager::class)
        );

        $attentdu = $infosServiceTest->mortDunJoueur($joueurMock);

        $this->assertIsObject($attentdu);
        $this->assertEquals(
            'Toto, Coupeur de citron Hobbit de <a href="/team/25">Totomen</a> est mort !',
            $attentdu->getMessages()
        );
    }
}