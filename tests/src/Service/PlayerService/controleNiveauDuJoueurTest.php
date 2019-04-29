<?php

namespace App\Tests\src\Service\PlayerService;


use App\Entity\GameDataPlayers;
use App\Entity\MatchData;
use App\Entity\Players;
use App\Entity\Races;
use App\Entity\Stades;
use App\Entity\Teams;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class controleNiveauDuJoueurTest extends KernelTestCase
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
        $joueur->setStatus(1);
        $joueur->setOwnedByTeam($equipe);

        $this->entityManager->persist($joueur);

        $matchData = new MatchData;
        $matchData->setBh(1);
        $matchData->setMvp(1);
        $matchData->setFPlayer($joueur);

        $this->entityManager->persist($matchData);

        $this->entityManager->flush();
    }

    /**
     * @test
     */
    public function le_controle_est_bien_fait_sur_l_exp()
    {
        $playerService = self::$container->get('App\Service\PlayerService');

        $equipe = $this->entityManager->getRepository(Teams::class)->findOneBy(['name' => 'test EquipeListeActif']);

        $playerService->controleNiveauDuJoueur($equipe);

        /** @var Players $joueur */
        $joueur = $this->entityManager->getRepository(Players::class)->findOneBy(['name' => 'joueur test']);

        $this->assertEquals(9,$joueur->getStatus());
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

        $this->entityManager->remove($this->entityManager->getRepository(MatchData::class)->findOneBy(['fPlayer'=>$joueur]));

        $this->entityManager->remove($joueur);
        $this->entityManager->remove($equipe);

        $this->entityManager->flush();
    }
}