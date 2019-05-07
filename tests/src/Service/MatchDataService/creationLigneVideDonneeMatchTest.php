<?php


namespace App\Tests\src\Service\MatchDataService;


use App\Entity\GameDataPlayers;
use App\Entity\GameDataStadium;
use App\Entity\MatchData;
use App\Entity\Matches;
use App\Entity\Meteo;
use App\Entity\Players;
use App\Entity\Races;
use App\Entity\Stades;
use App\Entity\Teams;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class creationLigneVideDonneeMatchTest extends KernelTestCase
{
    private $entityManager;

    protected function setUp()
    {

        self::bootKernel();

        $container  = self::$container;

        $this->entityManager = $container
            ->get('doctrine')
            ->getManager();

        $equipe1 = new Teams;

        $equipe1->setFRace($this->entityManager->getRepository(Races::class)->findOneBy(['raceId' => 9]));
        $equipe1->setName('test Equiperm0');
        $equipe1->setYear(3);
        $equipe1->setFStades($this->entityManager->getRepository(Stades::class)->findOneBy(['id' => 0]));

        $this->entityManager->persist($equipe1);

        $joueur = new Players;

        $joueur->setFPos($this->entityManager->getRepository(GameDataPlayers::class)->findOneBy(['posId' => 34]));
        $joueur->setName('joueur test');
        $joueur->setStatus(1);
        $joueur->setOwnedByTeam($equipe1);

        $this->entityManager->persist($joueur);

        $match = new Matches;
        $match->setTeam1($equipe1);
        $match->setFMeteo($this->entityManager->getRepository(Meteo::class)->findOneBy(['id'=> 0]));
        $match->setFStade($this->entityManager->getRepository(GameDataStadium::class)->findOneBy(['id'=> 0]));

        $this->entityManager->persist($match);

        $this->entityManager->flush();
    }

    /**
     * @test
     */
    public function la_ligne_matchdata_est_bien_cree()
    {
        $matchDataService = self::$container->get('App\Service\MatchDataService');

        $equipe =  $this->entityManager->getRepository(Teams::class)->findOneBy(['name' => 'test Equiperm0']);

        $joueur = $this->entityManager->getRepository(Players::class)->findOneBy(['name' => 'joueur test']);

        $matchDataService->creationLigneVideDonneeMatch($joueur, $this->entityManager->getRepository(Matches::class)->findOneBy(['team1'=>$equipe]));

        $this->assertEquals(1,count($this->entityManager->getRepository(MatchData::class)->findOneBy(['fPlayer'=>$joueur])));
    }

    protected function tearDown()
    {
        $joueur = $this->entityManager->getRepository(Players::class)->findOneBy(['name' => 'joueur test']);

        $equipe =  $this->entityManager->getRepository(Teams::class)->findOneBy(['name' => 'test Equiperm0']);

        $this->entityManager->remove($this->entityManager->getRepository(MatchData::class)->findOneBy(['fPlayer'=>$joueur]));

        $this->entityManager->remove($this->entityManager->getRepository(Matches::class)->findOneBy(['team1'=>$equipe]));

        $this->entityManager->remove($joueur);

        $this->entityManager->remove($equipe);

        $this->entityManager->flush();
    }
}