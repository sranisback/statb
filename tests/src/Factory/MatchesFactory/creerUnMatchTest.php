<?php

namespace App\Tests\src\Factory\MatchesFactory;


use App\Entity\GameDataStadium;
use App\Entity\Matches;
use App\Entity\Meteo;
use App\Entity\Stades;
use App\Entity\Teams;
use App\Factory\MatchesFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class creerUnMatchTest extends KernelTestCase
{
    /**
     * @test
     */
    public function un_match_est_cree(): void
    {
        $matchFactory = new MatchesFactory();

        $donnees = [
            'gain1' => '10000',
            'score1' => '5',
            'team_1' => '186',
            'varpop_team1' => '1',
            'gain2' => '10000',
            'score2' => '5',
            'team_2' => '181',
            'varpop_team2' => '0',
            'totalpop' => '2000',
            'stadeAccueil' => 2
        ];


        $this->assertInstanceOf(
            Matches::class,
            $matchFactory->creerUnMatch(
                $donnees,
                $this->createMock(Teams::class),
                $this->createMock(Teams::class),
                150_000,
                140_000,
                $this->createMock(Meteo::class),
                $this->createMock(GameDataStadium::class)
            )
        );
    }

    /**
     * @test
     */
    public function le_stade_de_l_equipe_1_est_utilise()
    {
        $matchFactory = new MatchesFactory();

        $stade = new Stades();
        $stade->setNiveau(2);

        $equipe1Test = new Teams();
        $equipe1Test->setFStades($stade);

        $donnees = [
            'gain1' => '10000',
            'score1' => '5',
            'team_1' => '186',
            'varpop_team1' => '1',
            'gain2' => '10000',
            'score2' => '5',
            'team_2' => '181',
            'varpop_team2' => '0',
            'totalpop' => '2000',
            'stadeAccueil' => 1
        ];

        $matchTest = $matchFactory->creerUnMatch(
            $donnees,
            $equipe1Test,
            $this->createMock(Teams::class),
            150_000,
            140_000,
            $this->createMock(Meteo::class),
            $this->createMock(GameDataStadium::class)
        );

        $this->assertInstanceOf(
            Matches::class,
            $matchTest
        );

        $this->assertEquals(2, $matchTest->getStadeAcceuil());
    }
}