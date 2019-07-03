<?php

namespace App\Tests\src\Service\DefisService;


use App\Entity\Defis;
use App\Entity\Races;
use App\Entity\Stades;
use App\Entity\Teams;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class creerDefisTest extends KernelTestCase
{
    private $entityManager;

    public function setUp()
    {
        self::bootKernel();
        $container = self::$container;

        $this->entityManager = $container
            ->get('doctrine')
            ->getManager();

        $equipe = new Teams;
        $equipe->setRerolls(4);
        $equipe->setFRace($this->entityManager->getRepository(Races::class)->findOneBy(['raceId' => 9]));
        $equipe->setName('test Equipe0');
        $equipe->setYear(3);
        $equipe->setFStades($this->entityManager->getRepository(Stades::class)->findOneBy(['id' => 0]));

        $equipe2 = new Teams;
        $equipe2->setRerolls(4);
        $equipe2->setFRace($this->entityManager->getRepository(Races::class)->findOneBy(['raceId' => 9]));
        $equipe2->setName('test Equipe1');
        $equipe2->setYear(3);
        $equipe2->setFStades($this->entityManager->getRepository(Stades::class)->findOneBy(['id' => 0]));

        $this->entityManager->persist($equipe);
        $this->entityManager->persist($equipe2);

        $this->entityManager->flush();
    }

    /**
     * @test
     */
    public function le_defis_est_cree()
    {
        $defisService = self::$container->get('App\Service\DefisService');

        $equipeOrigine = $this->entityManager->getRepository(Teams::class)->findOneBy(['name' => 'test Equipe0']);
        $equipeDefiee =  $this->entityManager->getRepository(Teams::class)->findOneBy(['name' => 'test Equipe1']);

        /** @var Teams $equipeOrigine */
        $datas['equipeOrigine'] = $equipeOrigine->getTeamId();
        /** @var Teams $equipeDefiee */
        $datas['equipeDefiee'] = $equipeDefiee->getTeamId();

        $this->assertInstanceOf(Defis::class, $defisService->creerDefis($datas));
    }

    public function tearDown()
    {
        /** @var Teams $equipeOrigine */
        $equipeOrigine = $this->entityManager->getRepository(Teams::class)->findOneBy(['name' => 'test Equipe0']);

        $this->entityManager->remove($this->entityManager->getRepository(Defis::class)->findOneBy(['equipeOrigine' => $equipeOrigine->getTeamId()]));
        $this->entityManager->remove($equipeOrigine);
        $this->entityManager->remove($this->entityManager->getRepository(Teams::class)->findOneBy(['name' => 'test Equipe1']));

        $this->entityManager->flush();
    }
}