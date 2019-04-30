<?php

namespace App\src\Service\EquipeService;

use App\Entity\Matches;
use App\Entity\Races;
use App\Entity\Stades;
use App\Entity\Teams;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AjoutInducementTest extends KernelTestCase
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
        $equipe1->setName('test EquipeAi0');
        $equipe1->setYear(3);
        $equipe1->setFStades($this->entityManager->getRepository(Stades::class)->findOneBy(['id' => 0]));
        $equipe1->setTreasury(500000);

        $this->entityManager->persist($equipe1);

        $equipe2 = new Teams;

        $equipe2->setFRace($this->entityManager->getRepository(Races::class)->findOneBy(['raceId' => 9]));
        $equipe2->setName('test EquipeAi1');
        $equipe2->setYear(3);
        $equipe2->setFStades($this->entityManager->getRepository(Stades::class)->findOneBy(['id' => 0]));

        $this->entityManager->persist($equipe2);

        $this->entityManager->flush();
    }

    /**
     * @test
     */
    public function le_cout_des_rr_change_quand_l_equipe_a_un_match()
    {
        $equipeService = self::$container->get('App\Service\EquipeService');
        $playerService = self::$container->get('App\Service\PlayerService');

        /** @var Teams $equipe1 */
        $equipe1 = $this->entityManager->getRepository(Teams::class)->findOneBy(['name' => 'test EquipeAi0']);
        $equipe2 = $this->entityManager->getRepository(Teams::class)->findOneBy(['name' => 'test EquipeAi1']);

        $match = new Matches;

        $match->setTeam1($equipe1);
        $match->setTeam2($equipe2);
        $match->setTeam1Score(1);
        $match->setTeam2Score(0);

        $this->entityManager->persist($match);

        $this->entityManager->flush();

        $return = $equipeService->ajoutInducement(
            $this->entityManager->getRepository(Teams::class)->findOneBy(['name' => 'test EquipeAi0']),
            'rr',
            $playerService
        );

        $this->assertEquals(1, $equipe1->getRerolls());
        $this->assertEquals(100000, $return['inducost']);
    }

    /**
     * @test
     */
    public function la_pop_ne_monte_plus_apres_un_match()
    {
        $equipeService = self::$container->get('App\Service\EquipeService');
        $playerService = self::$container->get('App\Service\PlayerService');

        /** @var Teams $equipe1 */
        $equipe1 = $this->entityManager->getRepository(Teams::class)->findOneBy(['name' => 'test EquipeAi0']);
        $equipe2 = $this->entityManager->getRepository(Teams::class)->findOneBy(['name' => 'test EquipeAi1']);

        $match = new Matches;

        $match->setTeam1($equipe1);
        $match->setTeam2($equipe2);
        $match->setTeam1Score(1);
        $match->setTeam2Score(0);

        $this->entityManager->persist($match);

        $this->entityManager->flush();

        $equipeService->ajoutInducement(
            $this->entityManager->getRepository(Teams::class)->findOneBy(['name' => 'test EquipeAi0']),
            'pop',
            $playerService
        );

        $this->assertEquals(0, $equipe1->getFfBought());
        $this->assertEquals(500000, $equipe1->getTreasury());
    }

    /**
     * @test
     */
    public function le_cout_des_rr_avant_matchs()
    {
        $equipeService = self::$container->get('App\Service\EquipeService');
        $playerService = self::$container->get('App\Service\PlayerService');

        /** @var Teams $equipe1 */
        $equipe1 = $this->entityManager->getRepository(Teams::class)->findOneBy(['name' => 'test EquipeAi0']);

        $return = $equipeService->ajoutInducement(
            $this->entityManager->getRepository(Teams::class)->findOneBy(['name' => 'test EquipeAi0']),
            'rr',
            $playerService
        );

        $this->assertEquals(1, $equipe1->getRerolls());
        $this->assertEquals(50000,  $return['inducost']);
    }

    public function tearDown()
    {
        $equipe1 = $this->entityManager->getRepository(Teams::class)->findOneBy(['name' => 'test EquipeAi0']);
        $equipe2 = $this->entityManager->getRepository(Teams::class)->findOneBy(['name' => 'test EquipeAi1']);

        foreach ($this->entityManager->getRepository(Matches::class)->findBy(['team1' => $equipe1]) as $matches) {
            $this->entityManager->remove($matches);
        }

        $this->entityManager->remove($equipe1);
        $this->entityManager->remove($equipe2);

        $this->entityManager->flush();
    }
}