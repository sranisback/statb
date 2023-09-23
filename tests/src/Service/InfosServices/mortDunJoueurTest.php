<?php


namespace App\Tests\src\Service\InfosServices;


use App\Entity\DeadPlayerInfo;
use App\Entity\GameDataPlayers;
use App\Entity\GameDataPlayersBb2020;
use App\Entity\Players;
use App\Entity\Races;
use App\Entity\RacesBb2020;
use App\Entity\Teams;
use App\Enum\RulesetEnum;
use App\Repository\DeadPlayerInfoRepository;
use App\Service\InfosService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

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

        $deadPlayerInfo = new DeadPlayerInfo();
        $deadPlayerInfo->setPhrase('* est mort !');

        $deadPlayerInfoRepo = $this->createMock(DeadPlayerInfoRepository::class);
        $deadPlayerInfoRepo->method('findAll')->willReturn([$deadPlayerInfo]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($deadPlayerInfoRepo);

        $envMock = $this->createMock(ContainerBagInterface::class);
        $envMock->method('get')->willReturn('dev');

        $infosServiceTest = new InfosService(
            $objectManager,
            $envMock
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

        $deadPlayerInfo = new DeadPlayerInfo();
        $deadPlayerInfo->setPhrase('* est mort !');

        $deadPlayerInfoRepo = $this->createMock(DeadPlayerInfoRepository::class);
        $deadPlayerInfoRepo->method('findAll')->willReturn([$deadPlayerInfo]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($deadPlayerInfoRepo);

        $envMock = $this->createMock(ContainerBagInterface::class);
        $envMock->method('get')->willReturn('dev');

        $infosServiceTest = new InfosService(
            $objectManager,
            $envMock
        );

        $attentdu = $infosServiceTest->mortDunJoueur($joueurMock);

        $this->assertIsObject($attentdu);
        $this->assertEquals(
            'Toto, Coupeur de citron Hobbit de <a href="/team/25">Totomen</a> est mort !',
            $attentdu->getMessages()
        );
    }
}