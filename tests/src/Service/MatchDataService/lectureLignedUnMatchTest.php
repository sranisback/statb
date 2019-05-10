<?php

namespace App\Tests\src\Service\MatchDataService;


use App\Entity\GameDataStadium;
use App\Entity\MatchData;
use App\Entity\Matches;
use App\Entity\Meteo;
use App\Entity\Players;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class lectureLignedUnMatchTest extends KernelTestCase
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
        $match->setFMeteo($this->entityManager->getRepository(Meteo::class)->findOneBy(['id'=> 0]));
        $match->setFStade($this->entityManager->getRepository(GameDataStadium::class)->findOneBy(['id'=> 0]));
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
    public function ligne_lue_matchdata()
    {
        $matchDataService = self::$container->get('App\Service\MatchDataService');

        $joueur = $this->entityManager->getRepository(Players::class)->findOneBy(['name' => 'joueur test']);

        $matchData = $this->entityManager->getRepository(MatchData::class)->findOneBy(['fPlayer'=>$joueur]);

        $this->assertEquals('MVP: 1, ', $matchDataService->lectureLignedUnMatch($matchData));
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