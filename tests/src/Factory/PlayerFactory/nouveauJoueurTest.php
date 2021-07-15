<?php

namespace App\Tests\src\Factory\PlayerFactory;


use App\Entity\Coaches;
use App\Entity\GameDataPlayers;
use App\Entity\GameDataPlayersBb2020;
use App\Entity\Players;
use App\Entity\PlayersIcons;
use App\Entity\Races;
use App\Entity\RacesBb2020;
use App\Entity\Teams;
use App\Enum\RulesetEnum;
use App\Factory\PlayerFactory;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class nouveauJoueurTest extends KernelTestCase
{
    /**
     * @test
     */
    public function un_nouveau_joueur_bb_2016_est_bien_cree(): void
    {
        $raceMock = $this->createMock(Races::class);

        $gameDataPlayersMock = $this->createMock(GameDataPlayers::class);
        $gameDataPlayersMock->method('getFRace')->willReturn($raceMock);
        $gameDataPlayersMock->method('getCost')->willReturn(50_000);

        $this->createMock(PlayersIcons::class);

        $coachMock = $this->createMock(Coaches::class);

        $equipeMock = $this->createMock(Teams::class);
        $equipeMock->method('getOwnedByCoach')->willReturn($coachMock);

        $playerIconRepoMock = $this->getMockBuilder(PlayersIcons::class)
            ->addMethods(['toutesLesIconesDunePosition'])
            ->getMock();
        $playerIconRepoMock->method('toutesLesIconesDunePosition')
            ->willReturn([$this->createMock(PlayersIcons::class)]);

        $entityManagerMock = $this->createMock(EntityManager::class);
        $entityManagerMock->method('getRepository')->willReturn($playerIconRepoMock);

        $this->assertInstanceOf(
            Players::class,
            PlayerFactory::nouveauJoueur(
                $gameDataPlayersMock,
                1,
                $equipeMock,
                1,
                $entityManagerMock,
                RulesetEnum::BB_2016,
                'Test'
            )
        );
    }

    /**
     * @test
     */
    public function un_nouveau_joueur_bb_2020_est_bien_cree(): void
    {
        $raceMock = $this->createMock(RacesBb2020::class);

        $gameDataPlayersBb2020Mock = $this->createMock(GameDataPlayersBb2020::class);
        $gameDataPlayersBb2020Mock->method('getRace')->willReturn($raceMock);
        $gameDataPlayersBb2020Mock->method('getCost')->willReturn(50_000);

        $this->createMock(PlayersIcons::class);

        $coachMock = $this->createMock(Coaches::class);

        $equipeMock = $this->createMock(Teams::class);
        $equipeMock->method('getOwnedByCoach')->willReturn($coachMock);

        $playerIconRepoMock = $this->getMockBuilder(PlayersIcons::class)
            ->addMethods(['toutesLesIconesDunePositionBb2020'])
            ->getMock();
        $playerIconRepoMock->method('toutesLesIconesDunePositionBb2020')
            ->willReturn([$this->createMock(PlayersIcons::class)]);

        $entityManagerMock = $this->createMock(EntityManager::class);
        $entityManagerMock->method('getRepository')->willReturn($playerIconRepoMock);

        $this->assertInstanceOf(
            Players::class,
            PlayerFactory::nouveauJoueur(
                $gameDataPlayersBb2020Mock,
                1,
                $equipeMock,
                1,
                $entityManagerMock,
                RulesetEnum::BB_2020,
                'Test'
            )
        );
    }

    /**
     * @test
     */
    public function il_n_y_a_pas_d_icones(): void
    {
        $raceMock = $this->createMock(Races::class);

        $gameDataPlayersMock = $this->createMock(GameDataPlayers::class);
        $gameDataPlayersMock->method('getFRace')->willReturn($raceMock);
        $gameDataPlayersMock->method('getCost')->willReturn(50_000);

        $playerIconTestDefaut = new PlayersIcons();
        $playerIconTestDefaut->setIconName('nope');

        $coachMock = $this->createMock(Coaches::class);

        $equipeMock = $this->createMock(Teams::class);
        $equipeMock->method('getOwnedByCoach')->willReturn($coachMock);

        $playerIconRepoMock = $this->getMockBuilder(PlayersIcons::class)
            ->addMethods(['toutesLesIconesDunePosition', 'findOneBy'])
            ->getMock();
        $playerIconRepoMock->method('toutesLesIconesDunePosition')
            ->willReturn([]);
        $playerIconRepoMock->method('findOneBy')->willReturn($playerIconTestDefaut);

        $entityManagerMock = $this->createMock(EntityManager::class);
        $entityManagerMock->method('getRepository')->willReturn($playerIconRepoMock);

        $playerTest = PlayerFactory::nouveauJoueur(
            $gameDataPlayersMock,
            1,
            $equipeMock,
            1,
            $entityManagerMock,
            RulesetEnum::BB_2016,
            'test'
        );

        $this->assertEquals('nope', $playerTest->getIcon()->getIconName());
    }
}