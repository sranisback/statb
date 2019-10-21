<?php

namespace App\Tests\src\Factory\PlayerFactory;


use App\Entity\Coaches;
use App\Entity\GameDataPlayers;
use App\Entity\Players;
use App\Entity\Races;
use App\Entity\Teams;
use App\Factory\PlayerFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class nouveauJoueurTest extends KernelTestCase
{
    /**
     * @test
     */
    public function un_nouveau_joueur_est_bien_cree()
    {
        $raceMock = $this->createMock(Races::class);

        $gameDataPlayersMock = $this->createMock(GameDataPlayers::class);
        $gameDataPlayersMock->method('getFRace')->willReturn($raceMock);
        $gameDataPlayersMock->method('getCost')->willReturn(50000);

        $coachMock = $this->createMock(Coaches::class);

        $equipeMock = $this->createMock(Teams::class);
        $equipeMock->method('getOwnedByCoach')->willReturn($coachMock);

        $playerFactory = new PlayerFactory();

        $this->assertInstanceOf(
            Players::class,
            $playerFactory->nouveauJoueur(
                $gameDataPlayersMock,
                1,
                $equipeMock,
                1
            )
        );
    }

}