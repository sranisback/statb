<?php

namespace App\Tests\src\Factory\PlayerFactory;


use App\Entity\Coaches;
use App\Entity\GameDataPlayers;
use App\Entity\Players;
use App\Entity\PlayersIcons;
use App\Entity\Races;
use App\Entity\Teams;
use App\Factory\PlayerFactory;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class nouveauJoueurTest extends KernelTestCase
{
    /**
     * @test
     */
    public function un_nouveau_joueur_est_bien_cree(): void
    {
        $raceMock = $this->createMock(Races::class);

        $gameDataPlayersMock = $this->createMock(GameDataPlayers::class);
        $gameDataPlayersMock->method('getFRace')->willReturn($raceMock);
        $gameDataPlayersMock->method('getCost')->willReturn(50_000);

        $playerIconMock = $this->createMock(PlayersIcons::class);

        $coachMock = $this->createMock(Coaches::class);

        $equipeMock = $this->createMock(Teams::class);
        $equipeMock->method('getOwnedByCoach')->willReturn($coachMock);

        $playerIconRepoMock = $this->getMockBuilder(PlayersIcons::class)
            ->setMethods(['toutesLesIconesDunePosition'])
            ->getMock();
        $playerIconRepoMock->method('toutesLesIconesDunePosition')
            ->willReturn([$this->createMock(PlayersIcons::class)]);

        $entityManagerMock = $this->createMock(EntityManager::class);
        $entityManagerMock->method('getRepository')->willReturn($playerIconRepoMock);

        $playerFactory = new PlayerFactory();

        $this->assertInstanceOf(
            Players::class,
            $playerFactory->nouveauJoueur(
                $gameDataPlayersMock,
                1,
                $equipeMock,
                1,
                'test',
                $entityManagerMock
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
            ->setMethods(['toutesLesIconesDunePosition', 'findOneBy'])
            ->getMock();
        $playerIconRepoMock->method('toutesLesIconesDunePosition')
            ->willReturn([]);
        $playerIconRepoMock->method('findOneBy')->willReturn($playerIconTestDefaut);

        $entityManagerMock = $this->createMock(EntityManager::class);
        $entityManagerMock->method('getRepository')->willReturn($playerIconRepoMock);

        $playerFactory = new PlayerFactory();

        $playerTest = $playerFactory->nouveauJoueur(
            $gameDataPlayersMock,
            1,
            $equipeMock,
            1,
            'test',
            $entityManagerMock
        );

        $this->assertEquals('nope', $playerTest->getIcon()->getIconName());
    }

}