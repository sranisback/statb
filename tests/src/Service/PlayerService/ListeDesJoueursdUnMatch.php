<?php

namespace App\Tests\src\Service\PlayerService;


use App\Entity\Matches;
use App\Entity\Teams;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ListeDesJoueursdUnMatch extends KernelTestCase
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
    public function retour_liste_joueur_d_un_match()
    {
        $playerService = self::$container->get('App\Service\PlayerService');
        $equipeService = self::$container->get('App\Service\EquipeService');

        $equipePourTest = $this->entityManager
            ->getRepository(Teams::class)->findOneBy(['name'=>'Les Ratgwents']);

        $MatchCollectionTest = $equipeService->listeDesMatchs($equipePourTest);

        foreach ( $MatchCollectionTest as $matchTest) {
            $listeDesJoueurs[] = $playerService->listeDesJoueursdUnMatch($matchTest,$equipePourTest);
        }
    }
}