<?php

namespace App\Tests\src\Service\EquipeService;


use App\Entity\GameDataPlayers;
use App\Entity\Players;
use App\Entity\Races;
use App\Entity\Stades;
use App\Entity\Teams;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class tvDelEquipeTest extends KernelTestCase
{
    private $entityManager;

    public function setUp()
    {
        self::bootKernel();
        $container = self::$container;

        $this->entityManager = $container
            ->get('doctrine')
            ->getManager();

        $equipe = new Teams;
        $equipe->setRerolls(4);
        $equipe->setFRace($this->entityManager->getRepository(Races::class)->findOneBy(['raceId' => 9]));
        $equipe->setName('test Equipetv0');
        $equipe->setYear(3);
        $equipe->setFStades($this->entityManager->getRepository(Stades::class)->findOneBy(['id' => 0]));

        $linemanHumain = $this->entityManager->getRepository(GameDataPlayers::class)->findOneBy(['posId' => 90]);

        $this->entityManager->persist($equipe);

        for ($i = 0; $i < 16; $i++) {
            $joueur = new Players();
            $joueur->setOwnedByTeam($equipe);
            $joueur->setFPos($linemanHumain);

            $this->entityManager->persist($joueur);
        }

        $this->entityManager->flush();
    }

    /**
     * @test
     */
    public function valider_calcul_tv()
    {
        $equipeService = self::$container->get('App\Service\EquipeService');
        $playerService = self::$container->get('App\Service\PlayerService');

        $this->assertEquals(
            1000000,
            $equipeService->tvDelEquipe(
                $this->entityManager->getRepository(Teams::class)->findOneBy(['name' => 'test Equipetv0']),
                $playerService
            )
        );
    }

    public function tearDown()
    {
        $equipe = $this->entityManager->getRepository(Teams::class)->findOneBy(['name' => 'test Equipetv0']);

        foreach ($this->entityManager->getRepository(Players::class)->findBy(['ownedByTeam' => $equipe]) as $joueur) {
            $this->entityManager->remove($joueur);
        }

        $this->entityManager->remove($equipe);
        $this->entityManager->flush();
    }
}

;