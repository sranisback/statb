<?php


namespace App\Tests\src\Service\InfosServices;


use App\Entity\Matches;
use App\Entity\Teams;
use App\Service\InfosService;
use Doctrine\ORM\EntityManager;
use Monolog\Test\TestCase;

class matchEnregistreTest extends TestCase
{
    /**
     * @test
     */
    public function le_message_est_bien_transmis()
    {
        $equipeMock0 = $this->createMock(Teams::class);
        $equipeMock0->method('getName')->willReturn('Test Equipe 1');

        $equipeMock1 = $this->createMock(Teams::class);
        $equipeMock1->method('getName')->willReturn('Test Equipe 2');

        $matchMock = $this->createMock(Matches::class);
        $matchMock->method('getTeam1')->willReturn($equipeMock0);
        $matchMock->method('getTeam2')->willReturn($equipeMock1);
        $matchMock->method('getMatchId')->willReturn(5);

        $infosServiceTest = new InfosService(
            $this->createMock(EntityManager::class)
        );

        $attentdu = $infosServiceTest->matchEnregistre($matchMock);

        $this->assertIsObject($attentdu);
        $this->assertEquals(
            'Match(5): Test Equipe 1 VS Test Equipe 2 enregistré. <a href="/match/5">voir</a>',
            $attentdu->getMessages()
        );

    }
}