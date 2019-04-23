<?php

namespace App\Tests\src\Service\PlayerService;


use App\Entity\MatchData;
use App\Entity\Matches;
use App\Entity\Players;
use App\Entity\Races;
use App\Entity\Stades;
use App\Entity\Teams;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class remplirMatchDataDeLigneAzeroTest extends KernelTestCase
{
    private $entityManager;

    protected function setUp()
    {
        self::bootKernel();
        $container = self::$container;

        $this->entityManager = $container
            ->get('doctrine')
            ->getManager();

        $equipe1 = new Teams;

        $equipe1->setFRace($this->entityManager->getRepository(Races::class)->findOneBy(['raceId' => 9]));
        $equipe1->setName('test EquiperemplirLigneZero0');
        $equipe1->setYear(3);
        $equipe1->setFStades($this->entityManager->getRepository(Stades::class)->findOneBy(['id' => 0]));

        $equipe2 = new Teams;

        $equipe2->setFRace($this->entityManager->getRepository(Races::class)->findOneBy(['raceId' => 9]));
        $equipe2->setName('test EquiperemplirLigneZero1');
        $equipe2->setYear(3);
        $equipe2->setFStades($this->entityManager->getRepository(Stades::class)->findOneBy(['id' => 0]));

        $joueur0 = new Players;
        $joueur0->setStatus(1);
        $joueur0->setOwnedByTeam($equipe1);

        $joueur1 = new Players;
        $joueur1->setStatus(1);
        $joueur1->setOwnedByTeam($equipe1);

        $joueur2 = new Players;
        $joueur2->setStatus(9);
        $joueur2->setOwnedByTeam($equipe1);

        $joueur3 = new Players;
        $joueur3->setStatus(7);
        $joueur3->setOwnedByTeam($equipe1);

        $joueur4 = new Players;
        $joueur4->setStatus(8);
        $joueur4->setOwnedByTeam($equipe1);

        $match = new Matches;

        $match->setTeam1($equipe1);
        $match->setTeam2($equipe2);
        $match->setTeam1Score(1);
        $match->setTeam2Score(0);

        $this->entityManager->persist($equipe1);
        $this->entityManager->persist($equipe2);
        $this->entityManager->persist($joueur0);
        $this->entityManager->persist($joueur1);
        $this->entityManager->persist($joueur2);
        $this->entityManager->persist($joueur3);
        $this->entityManager->persist($joueur4);
        $this->entityManager->persist($match);

        $this->entityManager->flush();
    }

    /**
     * @test
     */
    public function les_lignes_sont_creers()
    {
        $playerService = self::$container->get('App\Service\PlayerService');

        $equipe = $this->entityManager->getRepository(Teams::class)->findOneBy(
            ['name' => 'test EquiperemplirLigneZero0']
        );

        $match = $this->entityManager->getRepository(Matches::class)->findOneBy(['team1' => $equipe]);

        $playerService->remplirMatchDataDeLigneAzero(
            $equipe,
            $match
        );

        $this->assertEquals(
            3,
            count($this->entityManager->getRepository(MatchData::class)->findBy(['fMatch' => $match]))
        );
    }

    protected function tearDown()
    {
        self::bootKernel();
        $container = self::$container;

        $this->entityManager = $container
            ->get('doctrine')
            ->getManager();

        $equipe1 = $this->entityManager->getRepository(Teams::class)->findOneBy(
            ['name' => 'test EquiperemplirLigneZero0']
        );
        $equipe2 = $this->entityManager->getRepository(Teams::class)->findOneBy(
            ['name' => 'test EquiperemplirLigneZero1']
        );

        $match = $this->entityManager->getRepository(Matches::class)->findOneBy(['team1' => $equipe1]);

        foreach ($this->entityManager->getRepository(MatchData::class)->findBy(['fMatch' => $match]) as $matchData) {
            $this->entityManager->remove($matchData);
        }

        foreach ($this->entityManager->getRepository(Players::class)->findBy(['ownedByTeam' => $equipe1]) as $joueur) {
            $this->entityManager->remove($joueur);
        }

        $this->entityManager->remove($match);
        $this->entityManager->remove($equipe2);
        $this->entityManager->remove($equipe1);

        $this->entityManager->flush();
    }
}