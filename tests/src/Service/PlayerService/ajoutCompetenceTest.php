<?php

namespace App\Tests\src\Service\PlayerService;


use App\Entity\GameDataPlayers;
use App\Entity\GameDataSkills;
use App\Entity\Players;
use App\Entity\PlayersSkills;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ajoutCompetenceTest extends KernelTestCase
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

        $this->entityManager->flush();
    }

    /**
     * @test
     */
    public function la_competence_s_ajoute_correctement()
    {
        $playerService = self::$container->get('App\Service\PlayerService');

        $joueur = $this->entityManager->getRepository(Players::class)->findOneBy(['name' => 'joueur test']);

        $dataSkill = $this->entityManager->getRepository(GameDataSkills::class)->findOneBy(['skillId'=>1]);

        $this->assertEquals($this->entityManager->getRepository(PlayersSkills::class)->findOneBy(['fPid' => $joueur]), $playerService->ajoutCompetence($joueur,$dataSkill));
    }

    protected function tearDown()
    {
        $joueur = $this->entityManager->getRepository(Players::class)->findOneBy(['name' => 'joueur test']);

        $this->entityManager->remove($this->entityManager->getRepository(PlayersSkills::class)->findOneBy(['fPid' => $joueur]));

        $this->entityManager->remove($joueur);

        $this->entityManager->flush();
    }
}