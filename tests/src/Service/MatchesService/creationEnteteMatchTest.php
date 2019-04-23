<?php

namespace App\Tests\src\Service\MatchesService;


use App\Entity\Matches;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class creationEnteteMatchTest extends KernelTestCase
{
    private $entityManager;

    protected function setUp()
    {

        self::bootKernel();

        $container  = self::$container;

        $this->entityManager = $container
            ->get('doctrine')
            ->getManager();
    }

    /**
     * @test
     */
    public function verification_creation_entete_match()
    {
        $matchService = self::$container->get('App\Service\MatchesService');

        $data['team_1'] = 185;
        $data['team_2'] = 186;
        $data['totalpop'] = 25000;
        $data['varpop_team1'] = '-1';
        $data['varpop_team2'] = '1';
        $data['gain1'] = 50000;
        $data['gain2'] = 100000;
        $data['score1'] = 2;
        $data['score2'] = 0;

        $matchService->creationEnteteMatch($data);

       $this->assertInstanceOf(Matches::class,$matchService->creationEnteteMatch($data));

    }
}