<?php

namespace App\Tests\src\Service\PlayerService;


use App\Entity\MatchData;
use App\Entity\Matches;
use App\Entity\Players;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class actionDuJoueurDansUnMatchTest extends KernelTestCase
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

        $joueur->setName('joueur test');

        $this->entityManager->persist($joueur);

        $match = new Matches;

        $this->entityManager->persist($match);

        $matchData = new MatchData;

        $matchData->setFPlayer($joueur);
        $matchData->setFMatch($match);
        $matchData->setMvp(1);

        $this->entityManager->persist($matchData);

        $this->entityManager->flush();
    }

    /**
     * @test
     */
    public function les_actions_du_joueur_pour_un_match_sont_bien_retournees()
    {
        $playerService = self::$container->get('App\Service\PlayerService');
        $matchDataService = self::$container->get('App\Service\MatchDataService');

        $joueur = $this->entityManager->getRepository(Players::class)->findOneBy(['name' => 'joueur test']);

        $matchData = $this->entityManager->getRepository(MatchData::class)->findOneBy(['fPlayer'=>$joueur]);

        $match = $this->entityManager->getRepository(Matches::class)->findOneBy(['matchId'=>$matchData->getFMatch()->getMatchId()]);

        $this->assertEquals('MVP: 1, ',$playerService->actionDuJoueurDansUnMatch($match, $joueur, $matchDataService));
    }


    protected function tearDown()
    {
        $joueur = $this->entityManager->getRepository(Players::class)->findOneBy(['name' => 'joueur test']);

        $matchData = $this->entityManager->getRepository(MatchData::class)->findOneBy(['fPlayer'=>$joueur]);

        $match = $this->entityManager->getRepository(Matches::class)->findOneBy(['matchId'=>$matchData->getFMatch()->getMatchId()]);

        $this->entityManager->remove($matchData);
        $this->entityManager->remove($match);
        $this->entityManager->remove($joueur);

        $this->entityManager->flush();
    }

}