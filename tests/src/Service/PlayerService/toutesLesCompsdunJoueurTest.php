<?php

namespace App\Tests\src\Service\PlayerService;


use App\Entity\GameDataPlayers;
use App\Entity\GameDataSkills;
use App\Entity\Players;
use App\Entity\PlayersSkills;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class toutesLesCompsdunJoueurTest extends KernelTestCase
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

        $dataSkill = $this->entityManager->getRepository(GameDataSkills::class)->findOneBy(['skillId'=>1]);

        $compSupp = new PlayersSkills;

        $compSupp->setFPid($joueur);
        $compSupp->setType('N');
        $compSupp->setFSkill($dataSkill);


        $this->entityManager->persist($joueur);
        $this->entityManager->persist($compSupp);

        $this->entityManager->flush();
    }

    /**
     * @test
     */
    public function toutes_les_comps_sont_retournees()
    {
        $playerService = self::$container->get('App\Service\PlayerService');

        $joueur = $this->entityManager->getRepository(Players::class)->findOneBy(['name' => 'joueur test']);

        $retour = '<text class="test-primary">Frenzy</text>, <text class="test-primary">Dodge</text>, <text class="test-primary">Jump Up</text>, <text class="text-success">Block</text>, ';

        $this->assertEquals($playerService->toutesLesCompsdUnJoueur($joueur),$retour);
    }

    protected function tearDown()
    {
        $joueur = $this->entityManager->getRepository(Players::class)->findOneBy(['name' => 'joueur test']);

        $this->entityManager->remove($this->entityManager->getRepository(PlayersSkills::class)->findOneBy(['fPid' => $joueur]));

        $this->entityManager->remove($joueur);

        $this->entityManager->flush();
    }
}