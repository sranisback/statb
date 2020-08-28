<?php

namespace App\Tests\src\Service\MatchesService;


use App\Entity\Matches;
use App\Entity\Players;
use App\Factory\MatchDataFactory;
use App\Service\DefisService;
use App\Service\EquipeService;
use App\Service\MatchesService;
use App\Service\PlayerService;
use App\Service\SettingsService;
use Doctrine\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class enregistrementDesActionsDesJoueursTest extends TestCase
{
    /**
     * @test
     */
    public function les_donnees_sont_bien_transformees(): void
    {
        $joueurMock = $this->createMock(Players::class);
        $joueurMock->method('getPlayerId')->willReturn(1);

        $matchMock = $this->createMock(Matches::class);
        $matchMock->method('getMatchId')->willReturn(10);

        $matchDataTest = (new MatchDataFactory)->ligneVide($joueurMock, $matchMock);

        $matchDataRepo = $this->createMock(ObjectRepository::class);
        $matchDataRepo->method('findOneBy')->willReturn($matchDataTest);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchDataRepo);

        $matchesService = new MatchesService(
            $objectManager,
            $this->createMock(EquipeService::class),
            $this->createMock(PlayerService::class),
            $this->createMock(SettingsService::class),
            $this->createMock(DefisService::class)
        );

        $actionTest = [
            0 => [
                'id' => 1,
                'action' => 'COMP'
            ],
        ];

        $matchesService->enregistrementDesActionsDesJoueurs($actionTest, $matchMock);

        $this->assertEquals(1, $matchDataTest->getCp());
    }

    /**
     * @test
     */
    public function plusieurs_fois_la_meme_action(): void
    {
        $joueurMock = $this->createMock(Players::class);
        $joueurMock->method('getPlayerId')->willReturn(1);

        $matchMock = $this->createMock(Matches::class);
        $matchMock->method('getMatchId')->willReturn(10);

        $matchDataTest = (new MatchDataFactory)->ligneVide($joueurMock, $matchMock);

        $matchDataRepo = $this->createMock(ObjectRepository::class);
        $matchDataRepo->method('findOneBy')->willReturn($matchDataTest);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchDataRepo);

        $matchesService = new MatchesService(
            $objectManager,
            $this->createMock(EquipeService::class),
            $this->createMock(PlayerService::class),
            $this->createMock(SettingsService::class),
            $this->createMock(DefisService::class)
        );

        $actionTest = [
            0 => [
                'id' => 1,
                'action' => 'COMP'
            ],
            1 => [
                'id' => 1,
                'action' => 'COMP'
            ]
        ];

        $matchesService->enregistrementDesActionsDesJoueurs($actionTest, $matchMock);

        $this->assertEquals(2, $matchDataTest->getCp());
    }

    /**
     * @test
     */
    public function il_n_y_a_pas_de_donnees(): void
    {
        $joueurMock = $this->createMock(Players::class);
        $joueurMock->method('getPlayerId')->willReturn(1);

        $matchMock = $this->createMock(Matches::class);
        $matchMock->method('getMatchId')->willReturn(10);

        $matchDataTest = (new MatchDataFactory)->ligneVide($joueurMock, $matchMock);

        $matchDataRepo = $this->createMock(ObjectRepository::class);
        $matchDataRepo->method('findOneBy')->willReturn($matchDataTest);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchDataRepo);

        $matchesService = new MatchesService(
            $objectManager,
            $this->createMock(EquipeService::class),
            $this->createMock(PlayerService::class),
            $this->createMock(SettingsService::class),
            $this->createMock(DefisService::class)
        );

        $actionTest = [
        ];

        $matchesService->enregistrementDesActionsDesJoueurs($actionTest, $matchMock);

        $this->assertEquals(0, $matchDataTest->getCp());
    }

    /**
     * @test
     */
    public function actions_differentes(): void
    {
        $joueurMock = $this->createMock(Players::class);
        $joueurMock->method('getPlayerId')->willReturn(1);

        $matchMock = $this->createMock(Matches::class);
        $matchMock->method('getMatchId')->willReturn(10);

        $matchDataTest = (new MatchDataFactory)->ligneVide($joueurMock, $matchMock);

        $matchDataRepo = $this->createMock(ObjectRepository::class);
        $matchDataRepo->method('findOneBy')->willReturn($matchDataTest);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchDataRepo);

        $matchesService = new MatchesService(
            $objectManager,
            $this->createMock(EquipeService::class),
            $this->createMock(PlayerService::class),
            $this->createMock(SettingsService::class),
            $this->createMock(DefisService::class)
        );

        $actionTest = [
            0 => [
                'id' => 1,
                'action' => 'COMP'
            ],
            1 => [
                'id' => 1,
                'action' => 'CAS - BH'
            ],
        ];

        $matchesService->enregistrementDesActionsDesJoueurs($actionTest, $matchMock);

        $this->assertEquals(1, $matchDataTest->getCp());
        $this->assertEquals(1, $matchDataTest->getBh());
    }

    /**
     * @test
     */
    public function des_joueurs_differents(): void
    {
        $joueurMock0 = $this->createMock(Players::class);
        $joueurMock0->method('getPlayerId')->willReturn(1);
        $matchMock = $this->createMock(Matches::class);
        $matchMock->method('getMatchId')->willReturn(10);

        $matchDataTest0 = (new MatchDataFactory)->ligneVide($joueurMock0, $matchMock);

        $joueurMock1 = $this->createMock(Players::class);
        $joueurMock1->method('getPlayerId')->willReturn(2);

        $matchDataTest1 = (new MatchDataFactory)->ligneVide($joueurMock1, $matchMock);

        $matchDataRepo = $this->createMock(ObjectRepository::class);
        $matchDataRepo->method('findOneBy')->willReturnOnConsecutiveCalls($matchDataTest0,$matchDataTest1);

        $playerRepo = $this->createMock(ObjectRepository::class);
        $playerRepo->method('findOneBy')->willReturnOnConsecutiveCalls($joueurMock0, $joueurMock1);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->will(
            $this->returnCallback(
                function ($entityName) use ($matchDataRepo, $playerRepo) {
                    if ($entityName === 'App\Entity\MatchData') {
                        return $matchDataRepo;
                    }

                    if ($entityName === 'App\Entity\Players') {
                        return $playerRepo;
                    }
                }
            )
        );

        $matchesService = new MatchesService(
            $objectManager,
            $this->createMock(EquipeService::class),
            $this->createMock(PlayerService::class),
            $this->createMock(SettingsService::class),
            $this->createMock(DefisService::class)
        );

        $actionTest = [
            0 => [
                'id' => 1,
                'action' => 'COMP'
            ],
            1 => [
                'id' => 2,
                'action' => 'CAS - BH'
            ],
        ];

        $matchesService->enregistrementDesActionsDesJoueurs($actionTest, $matchMock);

        $this->assertEquals(1, $matchDataTest0->getCp());
        $this->assertEquals(1, $matchDataTest1->getBh());
    }
}
