<?php

namespace App\Tests\src\Service\EquipeService;


use App\Entity\Races;
use App\Entity\Stades;
use App\Entity\Teams;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class valeurInducementDelEquipeTest extends KernelTestCase
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
        $equipe->setFfBought(6);
        $equipe->setFf(4);
        $equipe->setCheerleaders(10);
        $equipe->setAssCoaches(5);
        $equipe->setApothecary(1);

        $equipe->setFRace($this->entityManager->getRepository(Races::class)->findOneBy(['raceId' => 9]));
        $equipe->setName('test Equipevi');
        $equipe->setYear(3);
        $equipe->setFStades($this->entityManager->getRepository(Stades::class)->findOneBy(['id' => 0]));

        $this->entityManager->persist($equipe);

        $this->entityManager->flush();
    }

    /**
     * @test
     */
    public function valider_calcul_total_inducement()
    {
        $equipeService = self::$container->get('App\Service\EquipeService');

        $inducementTest = $equipeService->valeurInducementDelEquipe(
            $this->entityManager->getRepository(Teams::class)->findOneBy(['name' => 'test Equipevi'])
        );

        $this->assertEquals(
            500000,
            $inducementTest['total']
        );
    }

    public function tearDown()
    {
        $equipe = $this->entityManager->getRepository(Teams::class)->findOneBy(['name' => 'test Equipevi']);

        $this->entityManager->remove($equipe);
        $this->entityManager->flush();
    }
}