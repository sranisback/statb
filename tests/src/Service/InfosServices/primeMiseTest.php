<?php


namespace App\Tests\src\Service\InfosServices;


use App\Entity\GameDataPlayers;
use App\Entity\Players;
use App\Entity\Primes;
use App\Entity\Races;
use App\Entity\Teams;
use App\Service\InfosService;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;

class primeMiseTest extends TestCase
{
    /**
     * @test
     */
    public function le_text_s_affiche_bien()
    {
        $raceMock = $this->createMock(Races::class);
        $raceMock->method('getName')->willReturn('Hobbit');

        $equipeMock = $this->createMock(Teams::class);
        $equipeMock->method('getName')->willReturn('test');

        $positionMock = $this->createMock(GameDataPlayers::class);
        $positionMock->method('getPos')->willReturn('Coupeur de citrons');

        $joueurMock = $this->createMock(Players::class);
        $joueurMock->method('getName')->willReturn('Toto');
        $joueurMock->method('getFPos')->willReturn($positionMock);
        $joueurMock->method('getFRid')->willReturn($raceMock);
        $joueurMock->method('getOwnedByTeam')->willReturn($equipeMock);

        $primeMock = $this->createMock(Primes::class);
        $primeMock->method('getPlayers')->willReturn($joueurMock);
        $primeMock->method('getMontant')->willReturn(50000);

        $infosServiceTest = new InfosService(
            $this->createMock(EntityManager::class)
        );

        $attentdu = $infosServiceTest->primeMise($primeMock);

        $this->assertIsObject($attentdu);
        $this->assertEquals(
            'Toto, Coupeur de citrons Hobbit de test a une prime de 50000 Po',
            $attentdu->getMessages()
        );
    }

}