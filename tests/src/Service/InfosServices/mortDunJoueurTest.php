<?php


namespace App\Tests\src\Service\InfosServices;


use App\Entity\GameDataPlayers;
use App\Entity\Players;
use App\Entity\Races;
use App\Entity\Teams;
use App\Service\InfosService;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;

class mortDunJoueurTest extends TestCase
{
    /**
     * @test
     */
    public function le_message_est_bien_forme()
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