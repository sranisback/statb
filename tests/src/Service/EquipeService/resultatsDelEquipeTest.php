<?php


namespace App\Tests\src\Service\EquipeService;


use App\Entity\GameDataStadium;
use App\Entity\Matches;
use App\Entity\Meteo;
use App\Entity\Races;
use App\Entity\Stades;
use App\Entity\Teams;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class resultatsDelEquipeTest extends KernelTestCase
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
        $equipe1->setName('test Equipere0');
        $equipe1->setYear(3);
        $equipe1->setFStades($this->entityManager->getRepository(Stades::class)->findOneBy(['id' => 0]));

        $this->entityManager->persist($equipe1);

        $equipe2 = new Teams;

        $equipe2->setFRace($this->entityManager->getRepository(Races::class)->findOneBy(['raceId' => 9]));
        $equipe2->setName('test Equipere1');
        $equipe2->setYear(3);
        $equipe2->setFStades($this->entityManager->getRepository(Stades::class)->findOneBy(['id' => 0]));

        $this->entityManager->persist($equipe2);

        $match1 = new Matches;

        $match1->setTeam1($equipe1);
        $match1->setTeam2($equipe2);
        $match1->setTeam1Score(1);
        $match1->setTeam2Score(0);
        $match1->setFMeteo($this->entityManager->getRepository(Meteo::class)->findOneBy(['id'=> 0]));
        $match1->setFStade($this->entityManager->getRepository(GameDataStadium::class)->findOneBy(['id'=> 0]));

        $this->entityManager->persist($match1);

        $match2 = new Matches;

        $match2->setTeam1($equipe1);
        $match2->setTeam2($equipe2);
        $match2->setTeam1Score(1);
        $match2->setTeam2Score(1);
        $match2->setFMeteo($this->entityManager->getRepository(Meteo::class)->findOneBy(['id'=> 0]));
        $match2->setFStade($this->entityManager->getRepository(GameDataStadium::class)->findOneBy(['id'=> 0]));

        $this->entityManager->persist($match2);

        $match3 = new Matches;

        $match3->setTeam1($equipe1);
        $match3->setTeam2($equipe2);
        $match3->setTeam1Score(0);
        $match3->setTeam2Score(1);
        $match3->setFMeteo($this->entityManager->getRepository(Meteo::class)->findOneBy(['id'=> 0]));
        $match3->setFStade($this->entityManager->getRepository(GameDataStadium::class)->findOneBy(['id'=> 0]));

        $this->entityManager->persist($match3);

        $this->entityManager->flush();
    }

    /**
     * @test
     */
    public function valider_resultats_equipe()
    {
        $equipeService = self::$container->get('App\Service\EquipeService');

        $equipe = $this->entityManager->getRepository(Teams::class)->findOneBy(['name' => 'test Equipere0']);

        $matchCollection = $this->entityManager->getRepository(Matches::class)->findBy(['team1' => $equipe]);

        $testResultat['win'] = 1;
        $testResultat['draw'] = 1;
        $testResultat['loss'] = 1;

        $this->assertEquals($testResultat, $equipeService->resultatsDelEquipe($equipe, $matchCollection));

    }

    public function tearDown()
    {
        $equipe1 = $this->entityManager->getRepository(Teams::class)->findOneBy(['name' => 'test Equipere0']);
        $equipe2 = $this->entityManager->getRepository(Teams::class)->findOneBy(['name' => 'test Equipere1']);

        foreach ($this->entityManager->getRepository(Matches::class)->findBy(['team1' => $equipe1]) as $matches) {
            $this->entityManager->remove($matches);
        }

        $this->entityManager->remove($equipe1);
        $this->entityManager->remove($equipe2);

        $this->entityManager->flush();
    }
}