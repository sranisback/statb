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

class primeGagneeTest extends TestCase
{
    /**
     * @test
     */
    public function le_text_s_affiche_bien()
    {
        $raceMock = $this->createMock(Races::class);
        $raceMock->method('getName')->willReturn('Hobbit');

        $equipeVictimeMock = $this->createMock(Teams::class);
        $equipeVictimeMock->method('getName')->willReturn('test');

        $equipeGagnanteMock = $this->createMock(Teams::class);
        $equipeGagnanteMock->method('getName')->willReturn('test');

        $positionMock = $this->createMock(GameDataPlayers::class);
        $positionMock->method('getPos')->willReturn('Coupeur de citrons');

        $joueurMock = $this->createMock(Players::class);
        $joueurMock->method('getName')->willReturn('Toto');
        $joueurMock->method('getFPos')->willReturn($positionMock);
        $joueurMock->method('getFRid')->willReturn($raceMock);
        $joueurMock->method('getOwnedByTeam')->willReturn($equipeVictimeMock);

        $primeMock = $this->createMock(Primes::class);
        $primeMock->method('getPlayers')->willReturn($joueurMock);
        $primeMock->method('getMontant')->willReturn(50000);
        $primeMock->method('getEquipePrime')->willReturn($equipeGagnanteMock);

        $infosServiceTest = new InfosService(
            $this->createMock(EntityManager::class)
        );

        $attentdu = $infosServiceTest->primeGagnee($primeMock);

        $this->assertIsObject($attentdu);
        $this->assertEquals(
            'test a touché la prime de 50000Po sur Toto, Coupeur de citrons Hobbit',
            $attentdu->getMessages()
        );
    }
}
