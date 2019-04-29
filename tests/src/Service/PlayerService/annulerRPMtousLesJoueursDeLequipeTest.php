<?php

namespace App\Tests\src\Service\PlayerService;


use App\Entity\GameDataPlayers;
use App\Entity\Players;
use App\Entity\Races;
use App\Entity\Stades;
use App\Entity\Teams;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class annulerRPMtousLesJoueursDeLequipeTest extends KernelTestCase
{
    private $entityManager;

    protected function setUp()
    {
        self::bootKernel();
        $container = self::$container;

        $this->entityManager = $container
            ->get('doctrine')
            ->getManager();

        $equipe = new Teams;
        $equipe->setYear(3);
        $equipe->setName('test EquipeListeActif');
        $equipe->setFRace($this->entityManager->getRepository(Races::class)->findOneBy(['raceId' => 9]));
        $equipe->setFStades($this->entityManager->getRepository(Stades::class)->findOneBy(['id' => 0]));

        $this->entityManager->persist($equipe);

        $joueur = new Players;

        $joueur->setFPos($this->entityManager->getRepository(GameDataPlayers::class)->findOneBy(['posId' => 34]));
        $joueur->setName('joueur test');
        $joueur->setType(1);
        $joueur->setStatus(1);
        $joueur->setInjRpm(1);
        $joueur->setOwnedByTeam($equipe);

        $this->entityManager->persist($joueur);

        $this->entityManager->flush();
    }

    /**
     * @test
     */
    public function les_rpm_de_l_equipe_sont_bien_supprime()
    {
        $playerService = self::$container->get('App\Service\PlayerService');

        $equipe = $this->entityManager->getRepository(Teams::class)->findOneBy(['name' => 'test EquipeListeActif']);

        $playerService->annulerRPMtousLesJoueursDeLequipe($equipe);

        /** @var Players $joueur */
        $joueur = $this->entityManager->getRepository(Players::class)->findOneBy(['name' => 'joueur test']);

        $this->assertEquals(0,$joueur->getInjRpm());
    }


    protected function tearDown()
    {
        self::bootKernel();
        $container = self::$container;

        $this->entityManager = $container
            ->get('doctrine')
            ->getManager();

        $equipe = $this->entityManager->getRepository(Teams::class)->findOneBy(['name' => 'test EquipeListeActif']);

        $joueur = $this->entityManager->getRepository(Players::class)->findOneBy(['name' => 'joueur test']);

        $this->entityManager->remove($joueur);
        $this->entityManager->remove($equipe);

        $this->entityManager->flush();
    }
}