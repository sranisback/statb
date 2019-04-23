<?php
/**
 * Created by PhpStorm.
 * User: Sran_isback
 * Date: 23/04/2019
 * Time: 16:56
 */

namespace App\Tests\src\Service\PlayerService;


use App\Entity\GameDataPlayers;
use App\Entity\MatchData;
use App\Entity\Players;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class actionsDuJoueurTest extends KernelTestCase
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

        $matchData = new MatchData;

        $matchData->setFPlayer($joueur);

        $this->entityManager->persist($matchData);

        $this->entityManager->flush();
    }


    /**
     * @test
     */
    public function les_actions_sont_retournees_correctement()
    {
        $playerService = self::$container->get('App\Service\PlayerService');

        $joueur = $this->entityManager->getRepository(Players::class)->findOneBy(['name' => 'joueur test']);

        $retour = [
            'NbrMatch' => 1,
            'cp' => 0,
            'td' => 0,
            'int' => 0,
            'cas' => 0,
            'mvp' => 0,
            'agg' => 0,
        ];

        $this->assertEquals($retour,$playerService->actionsDuJoueur($joueur));
    }

    protected function tearDown()
    {
        self::bootKernel();
        $container = self::$container;

        $this->entityManager = $container
            ->get('doctrine')
            ->getManager();

        $joueur = $this->entityManager->getRepository(Players::class)->findOneBy(['name' => 'joueur test']);

        $this->entityManager->remove($this->entityManager->getRepository(MatchData::class)->findOneBy(['fPlayer'=>$joueur]));

        $this->entityManager->remove($joueur);

        $this->entityManager->flush();
    }
}