<?php

namespace App\Tests\src\Service\PlayerService;


use App\Entity\GameDataPlayers;
use App\Entity\GameDataStadium;
use App\Entity\Players;
use App\Entity\Races;
use App\Entity\Stades;
use App\Entity\Teams;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ajoutJoueurTest extends KernelTestCase
{
    private $entityManager;

    protected function setUp()
    {
        self::bootKernel();
        $container = self::$container;

        $this->entityManager = $container
            ->get('doctrine')
            ->getManager();

        $stade = new Stades;
        $stade->setFTypeStade($this->entityManager->getRepository(GameDataStadium::class)->findOneBy(['id'=>0 ]));

        $this->entityManager->persist($stade);

        $equipe = new Teams;
        $equipe->setName('equipe test');
        $equipe->setTreasury(1000000);
        $equipe->setYear(3);
        $equipe->setFRace($this->entityManager->getRepository(Races::class)->findOneBy(['name'=>'Dark Elf' ]));
        $equipe->setFStades($stade);

        $this->entityManager->persist($equipe);

        $this->entityManager->flush();
    }

    /**
     * @test
     */
    public function le_joueur_est_bien_ajoute()
    {
        $playerService = self::$container->get('App\Service\PlayerService');

        /** @var GameDataPlayers $position */
        $position = $this->entityManager->getRepository(GameDataPlayers::class)->FindOneBy(['pos'=>'Witch Elf']);
        /** @var Teams $equipe */
        $equipe = $this->entityManager->getRepository(Teams::class)->findOneBy(['name' => 'equipe test']);

        $testRetour['resultat'] = 'ok';

        $retour =  $playerService->ajoutJoueur($position->getPosId(),$equipe->getTeamId());

        $this->assertEquals($testRetour['resultat'], $retour['resultat']);
    }

    protected function tearDown()
    {
        self::bootKernel();
        $container = self::$container;

        $this->entityManager = $container
            ->get('doctrine')
            ->getManager();

        $equipe = $this->entityManager->getRepository(Teams::class)->findOneBy(['name' => 'equipe test']);

        $this->entityManager->remove($this->entityManager->getRepository(Players::class)->findOneBy(['ownedByTeam'=>$equipe]));

        $this->entityManager->remove($equipe);

        $this->entityManager->remove($this->entityManager->getRepository(Stades::class)->findOneBy(['nom'=>null]));

        $this->entityManager->flush();
    }
}