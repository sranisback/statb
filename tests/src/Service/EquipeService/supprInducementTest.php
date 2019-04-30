<?php

namespace App\Tests\src\Service\EquipeService;


use App\Entity\Matches;
use App\Entity\Races;
use App\Entity\Stades;
use App\Entity\Teams;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class supprInducementTest extends KernelTestCase
{
    private $entityManager;

    public function setUp()
    {
        self::bootKernel();
        $container = self::$container;

        $this->entityManager = $container
            ->get('doctrine')
            ->getManager();

        $equipe1 = new Teams;

        $equipe1->setFRace($this->entityManager->getRepository(Races::class)->findOneBy(['raceId' => 9]));
        $equipe1->setName('test EquipeSi0');
        $equipe1->setYear(3);
        $equipe1->setFStades($this->entityManager->getRepository(Stades::class)->findOneBy(['id' => 0]));
        $equipe1->setTreasury(500000);

        $this->entityManager->persist($equipe1);

        $equipe2 = new Teams;

        $equipe2->setFRace($this->entityManager->getRepository(Races::class)->findOneBy(['raceId' => 9]));
        $equipe2->setName('test EquipeSi1');
        $equipe2->setYear(3);
        $equipe2->setFStades($this->entityManager->getRepository(Stades::class)->findOneBy(['id' => 0]));

        $this->entityManager->persist($equipe2);

        $this->entityManager->flush();
    }

    /**
     * @test
     */
    public function les_inducements_rendent_l_argent_si_pas_de_match()
    {
        $equipeService = self::$container->get('App\Service\EquipeService');
        $playerService = self::$container->get('App\Service\PlayerService');

        /** @var Teams $equipe */
        $equipe = $this->entityManager->getRepository(Teams::class)->findOneBy(['name' => 'test EquipeSi0']);
        $equipe->setFfBought(1);

        $this->entityManager->persist($equipe);

        $equipeService->supprInducement($equipe,'pop', $playerService);

        $this->assertEquals(510000,$equipe->getTreasury());
    }

    /**
     * @test
     */
    public function les_inducements_ne_rendent_pas_l_argent_si_match()
    {
        $equipeService = self::$container->get('App\Service\EquipeService');
        $playerService = self::$container->get('App\Service\PlayerService');

        /** @var Teams $equipe1 */
        $equipe1 = $this->entityManager->getRepository(Teams::class)->findOneBy(['name' => 'test EquipeSi0']);
        $equipe2 = $this->entityManager->getRepository(Teams::class)->findOneBy(['name' => 'test EquipeSi1']);

        $equipe1->setApothecary(1);

        $this->entityManager->persist($equipe1);

        $match = new Matches;

        $match->setTeam1($equipe1);
        $match->setTeam2($equipe2);
        $match->setTeam1Score(1);
        $match->setTeam2Score(0);

        $this->entityManager->persist($match);

        $equipeService->supprInducement($equipe1,'apo', $playerService);

        $this->assertEquals(550000,$equipe1->getTreasury());
    }

    public function tearDown()
    {
        $equipe1 = $this->entityManager->getRepository(Teams::class)->findOneBy(['name' => 'test EquipeSi0']);
        $equipe2 = $this->entityManager->getRepository(Teams::class)->findOneBy(['name' => 'test EquipeSi1']);

        foreach ($this->entityManager->getRepository(Matches::class)->findBy(['team1' => $equipe1]) as $matches) {
            $this->entityManager->remove($matches);
        }

        $this->entityManager->remove($equipe1);
        $this->entityManager->remove($equipe2);

        $this->entityManager->flush();
    }
}