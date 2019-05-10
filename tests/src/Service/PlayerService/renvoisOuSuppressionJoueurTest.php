<?php

namespace App\Tests\src\Service\PlayerService;


use App\Entity\GameDataPlayers;
use App\Entity\MatchData;
use App\Entity\Players;
use App\Entity\Races;
use App\Entity\Stades;
use App\Entity\Teams;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class renvoisOuSuppressionJoueurTest extends KernelTestCase
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
        $equipe->setName('test EquipeListe');
        $equipe->setTreasury(0);
        $equipe->setFRace($this->entityManager->getRepository(Races::class)->findOneBy(['raceId' => 9]));
        $equipe->setFStades($this->entityManager->getRepository(Stades::class)->findOneBy(['id' => 0]));

        $joueur0 = new Players;
        $joueur0->setOwnedByTeam($equipe);
        $joueur0->setFPos($this->entityManager->getRepository(GameDataPlayers::class)->findOneBy(['posId' => 34]));

        $this->entityManager->persist($equipe);
        $this->entityManager->persist($joueur0);

        $this->entityManager->flush();
    }

    /**
     * @test
     */
    public function le_joueur_est_supprime()
    {
        $playerService = self::$container->get('App\Service\PlayerService');

        /** @var Teams $equipe */
        $equipe = $this->entityManager->getRepository(Teams::class)->findOneBy(['name' => 'test EquipeListe']);

        $playerService->renvoisOuSuppressionJoueur($this->entityManager->getRepository(Players::class)->findOneBy(['ownedByTeam' => $equipe]));

        $this->assertEquals(110000, $equipe->getTreasury());
        $this->assertEmpty($this->entityManager->getRepository(Players::class)->findOneBy(['ownedByTeam' => $equipe]));
    }

    /**
     * @test
     */
    public function le_joueur_est_renvoye()
    {
        /** @var Teams $equipe */
        $equipe = $this->entityManager->getRepository(Teams::class)->findOneBy(['name' => 'test EquipeListe']);

        /** @var Players $joueur */
        $joueur = $this->entityManager->getRepository(Players::class)->findOneBy(['ownedByTeam' => $equipe]);

        $matchData = new MatchData;
        $matchData->setFPlayer($joueur);

        $this->entityManager->persist($matchData);

        $this->entityManager->flush();

        $playerService = self::$container->get('App\Service\PlayerService');

        $playerService->renvoisOuSuppressionJoueur($joueur);

        $this->entityManager->remove($matchData);

        $this->entityManager->flush();

        $this->assertEquals(0, $equipe->getTreasury());
        $this->assertEquals(7,$joueur->getStatus());
    }


    protected function tearDown()
    {
        $equipe = $this->entityManager->getRepository(Teams::class)->findOneBy(['name' => 'test EquipeListe']);

        foreach ($this->entityManager->getRepository(Players::class)->findBy(['ownedByTeam' => $equipe]) as $joueur) {
            //$matchData = $this->getEntityManager()->getRepository(MatchData::class)->findBy(['fPlayer' => $joueur]);
            //$this->entityManager->remove($matchData);
            $this->entityManager->remove($joueur);
        }

        $this->entityManager->remove($equipe);
        $this->entityManager->flush();
   }
}