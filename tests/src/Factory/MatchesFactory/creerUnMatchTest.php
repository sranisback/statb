<?php

namespace App\Tests\src\Factory\MatchesFactory;


use App\Entity\GameDataStadium;
use App\Entity\Matches;
use App\Entity\Meteo;
use App\Entity\Teams;
use App\Factory\MatchDataFactory;
use App\Factory\MatchesFactory;
use App\Factory\PlayerFactory;
use stdClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class creerUnMatchTest extends KernelTestCase
{
    /**
     * @test
     */
    public function un_match_est_cree()
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
                150000,
                140000,
                $this->createMock(Meteo::class),
                $this->createMock(GameDataStadium::class)
            )
        );
    }
}