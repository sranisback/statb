<?php

namespace App\Tests\src\Service\PlayerService;


use App\Entity\GameDataPlayers;
use App\Entity\Players;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class listenivSpeciauxEtSurcoutTest extends KernelTestCase
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
        $joueur->setAchSt(1);

        $this->entityManager->persist($joueur);

        $this->entityManager->flush();
    }

    /**
     * @test
     */
    public function tous_les_niv_spec_sont_retournes()
    {
        $playerService = self::$container->get('App\Service\PlayerService');

        $joueur = $this->entityManager->getRepository(Players::class)->findOneBy(['name' => 'joueur test']);

        $retour['nivspec'] = '<text class="text-success">+1 St</text>, ';
        $retour['cout'] = 50000;

        $this->assertEquals($playerService->listenivSpeciauxEtSurcout($joueur),$retour);
    }

    protected function tearDown()
    {
        $this->entityManager->remove($this->entityManager->getRepository(Players::class)->findOneBy(['name' => 'joueur test']));

        $this->entityManager->flush();
    }
}