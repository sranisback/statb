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
            'stadeAccueil' => 2,
            'depense1' => '10000',
            'depense2' => '10000',
        ];

        $this->assertInstanceOf(
            Matches::class,
            MatchesFactory::creerUnMatch(
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
            'stadeAccueil' => 2,
            'depense1' => 0,
            'depense2' => 0
        ];

        $matchTest = MatchesFactory::creerUnMatch(
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

    /**
     * @test
     */
    public function les_depenses_sont_a_double_quote()
    {
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
            'stadeAccueil' => 2,
            'depense1' => '',
            'depense2' => ''
        ];

        $matchTest = MatchesFactory::creerUnMatch(
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
    }
}
