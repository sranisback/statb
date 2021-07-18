<?php


namespace App\Tests\src\Service\InfosServices;

use App\Entity\Coaches;
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

class joueurEngageTest extends TestCase
{
    /**
     * @test
     */
    public function message_enregistre_pour_achat_joueur_bb2016()
    {
        $coachMock = $this->createMock(Coaches::class);
        $coachMock->method('getName')->willReturn('Zorro');

        $raceMock = $this->createMock(Races::class);
        $raceMock->method('getName')->willReturn('Hobbit');

        $equipeMock = $this->createMock(Teams::class);
        $equipeMock->method('getName')->willReturn('test');
        $equipeMock->method('getOwnedByCoach')->willReturn($coachMock);
        $equipeMock->method('getTeamId')->willReturn(1);
        $equipeMock->method('getRuleset')->willReturn(RulesetEnum::BB_2016);

        $positionMock = $this->createMock(GameDataPlayers::class);
        $positionMock->method('getPos')->willReturn('Coupeur de citrons');

        $joueurMock = $this->createMock(Players::class);
        $joueurMock->method('getName')->willReturn('Toto');
        $joueurMock->method('getFPos')->willReturn($positionMock);
        $joueurMock->method('getFRid')->willReturn($raceMock);
        $joueurMock->method('getOwnedByTeam')->willReturn($equipeMock);
        $joueurMock->method('getRuleset')->willReturn(RulesetEnum::BB_2016);

        $infosServiceTest = new InfosService(
            $this->createMock(EntityManager::class)
        );

        $attentdu = $infosServiceTest->joueurEngage($joueurMock);

        $this->assertIsObject($attentdu);
        $this->assertEquals(
            'Toto, Coupeur de citrons Hobbit a été engagé par <a href="/team/1">test</a> de Zorro',
            $attentdu->getMessages()
        );
    }

    /**
     * @test
     */
    public function message_enregistre_pour_achat_joueur_bb2020()
    {
        $coachMock = $this->createMock(Coaches::class);
        $coachMock->method('getName')->willReturn('Zorro');

        $raceMock = $this->createMock(RacesBb2020::class);
        $raceMock->method('getName')->willReturn('Hobbit');

        $equipeMock = $this->createMock(Teams::class);
        $equipeMock->method('getName')->willReturn('test');
        $equipeMock->method('getOwnedByCoach')->willReturn($coachMock);
        $equipeMock->method('getTeamId')->willReturn(1);
        $equipeMock->method('getRuleset')->willReturn(RulesetEnum::BB_2020);

        $positionMock = $this->createMock(GameDataPlayersBb2020::class);
        $positionMock->method('getPos')->willReturn('Coupeur de citrons');

        $joueurMock = $this->createMock(Players::class);
        $joueurMock->method('getName')->willReturn('Toto');
        $joueurMock->method('getFPosBb2020')->willReturn($positionMock);
        $joueurMock->method('getFRidBb2020')->willReturn($raceMock);
        $joueurMock->method('getOwnedByTeam')->willReturn($equipeMock);
        $joueurMock->method('getRuleset')->willReturn(RulesetEnum::BB_2020);

        $infosServiceTest = new InfosService(
            $this->createMock(EntityManager::class)
        );

        $attentdu = $infosServiceTest->joueurEngage($joueurMock);

        $this->assertIsObject($attentdu);
        $this->assertEquals(
            'Toto, Coupeur de citrons Hobbit a été engagé par <a href="/team/1">test</a> de Zorro',
            $attentdu->getMessages()
        );
    }
}
