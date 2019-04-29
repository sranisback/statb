<?php

namespace App\Tests\src\Service\PlayerService;


use App\Entity\GameDataPlayers;
use App\Entity\MatchData;
use App\Entity\Players;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class xpDuJoueurTest extends KernelTestCase
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
        $joueur->setType(1);

        $this->entityManager->persist($joueur);

        $matchData = new MatchData;

        $matchData->setMvp(1);
        $matchData->setFPlayer($joueur);

        $this->entityManager->persist($matchData);

        $this->entityManager->flush();
    }

    /**
     *@test
     */
    public function l_xp_est_bien_calculee()
    {
        $playerService = self::$container->get('App\Service\PlayerService');

        $joueur = $this->entityManager->getRepository(Players::class)->findOneBy(['name' => 'joueur test']);

        $this->assertEquals(5, $playerService->xpDuJoueur($joueur));
    }

    protected function tearDown()
    {
        self::bootKernel();
        $container = self::$container;

        $this->entityManager = $container
            ->get('doctrine')
            ->getManager();

        $joueur = $this->entityManager->getRepository(Players::class)->findOneBy(['name' => 'joueur test']);

        $this->entityManager->remove($this->entityManager->getRepository(MatchData::class)->findOneBy(['fPlayer'=>$joueur]));

        $this->entityManager->remove($joueur);

        $this->entityManager->flush();
    }
}