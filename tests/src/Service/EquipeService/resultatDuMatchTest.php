<?php

namespace App\Tests\src\Service\EquipeService;


use App\Entity\GameDataStadium;
use App\Entity\Matches;
use App\Entity\Meteo;
use App\Entity\Races;
use App\Entity\Stades;
use App\Entity\Teams;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class resultatDuMatchTest extends KernelTestCase
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
        $equipe1->setName('test Equiperm0');
        $equipe1->setYear(3);
        $equipe1->setFStades($this->entityManager->getRepository(Stades::class)->findOneBy(['id' => 0]));

        $this->entityManager->persist($equipe1);

        $equipe2 = new Teams;

        $equipe2->setFRace($this->entityManager->getRepository(Races::class)->findOneBy(['raceId' => 9]));
        $equipe2->setName('test Equiperm1');
        $equipe2->setYear(3);
        $equipe2->setFStades($this->entityManager->getRepository(Stades::class)->findOneBy(['id' => 0]));

        $this->entityManager->persist($equipe2);

        $match = new Matches;

        $match->setTeam1($equipe1);
        $match->setTeam2($equipe2);
        $match->setTeam1Score(1);
        $match->setTeam2Score(0);

        $match->setFMeteo($this->entityManager->getRepository(Meteo::class)->findOneBy(['id'=> 0]));
        $match->setFStade($this->entityManager->getRepository(GameDataStadium::class)->findOneBy(['id'=> 0]));

        $this->entityManager->persist($match);


        $this->entityManager->flush();
    }

    /**
     * @test
     */
    public function valider_resultat_du_match()
    {
        $equipeService = self::$container->get('App\Service\EquipeService');

        $equipe = $this->entityManager->getRepository(Teams::class)->findOneBy(['name' => 'test Equiperm0']);

        $match = $this->entityManager->getRepository(Matches::class)->findOneBy(['team1' => $equipe]);

        $testResultat['win'] = 1;
        $testResultat['draw'] = 0;
        $testResultat['loss'] = 0;

        $this->assertEquals($testResultat, $equipeService->resultatDuMatch($equipe, $match));

    }
    public function tearDown()
    {
        $equipe1 = $this->entityManager->getRepository(Teams::class)->findOneBy(['name' => 'test Equiperm0']);
        $equipe2 = $this->entityManager->getRepository(Teams::class)->findOneBy(['name' => 'test Equiperm1']);

        foreach ($this->entityManager->getRepository(Matches::class)->findBy(['team1' => $equipe1]) as $matches) {
            $this->entityManager->remove($matches);
        }

        $this->entityManager->remove($equipe1);
        $this->entityManager->remove($equipe2);

        $this->entityManager->flush();
    }
}