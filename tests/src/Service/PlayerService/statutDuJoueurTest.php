<?php

namespace App\Tests\src\Service\PlayerService;

use App\Entity\Players;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class statutDuJoueurTest extends KernelTestCase
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

        $joueur->setName('joueur test');

        $joueur->setStatus(9);

        $this->entityManager->persist($joueur);

        $this->entityManager->flush();
    }

    /**
     * @test
     */
    public function le_statut_est_bien_retourne()
    {
        $playerService = self::$container->get('App\Service\PlayerService');

        $joueur = $this->entityManager->getRepository(Players::class)->findOneBy(['name' => 'joueur test']);

        $this->assertEquals('XP',$playerService->statutDuJoueur($joueur));
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