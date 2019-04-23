<?php

namespace App\Tests\src\Service\PlayerService;


use App\Entity\GameDataPlayers;
use App\Entity\Players;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class statsDuJoueurTest extends KernelTestCase
{
    private $entityManager;

    protected function setUp()
    {
        self::bootKernel();
        $container = self::$container;

        $this->entityManager = $container
            ->get('doctrine')
            ->getManager();

        $joueur = new Players;

        $joueur->setFPos($this->entityManager->getRepository(GameDataPlayers::class)->findOneBy(['posId' => 34]));

        $joueur->setName('joueur test');

        $this->entityManager->persist($joueur);

        $this->entityManager->flush();
    }

    /**
     * @test
     */
    public function les_stats_du_joueur_sont_bien_retournee()
    {
        $playerService = self::$container->get('App\Service\PlayerService');

        $joueur = $this->entityManager->getRepository(Players::class)->findOneBy(['name' => 'joueur test']);

        $retour['comp'] = '<text class="test-primary">Frenzy</text>, <text class="test-primary">Dodge</text>, <text class="test-primary">Jump Up</text>';
        $retour['actions'] = [
            'NbrMatch' => 0,
            'cp' => 0,
            'td' => 0,
            'int' => 0,
            'cas' => 0,
            'mvp' => 0,
            'agg' => 0,
        ];

        $this->assertEquals($playerService->statsDuJoueur($joueur),$retour);
    }

    protected function tearDown()
    {
        self::bootKernel();
        $container = self::$container;

        $this->entityManager = $container
            ->get('doctrine')
            ->getManager();

        $this->entityManager->remove($this->entityManager->getRepository(Players::class)->findOneBy(['name' => 'joueur test']));

        $this->entityManager->flush();
    }
}