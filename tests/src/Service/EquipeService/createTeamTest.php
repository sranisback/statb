<?php

namespace App\Tests\src\Service\EquipeService;


use App\Entity\Races;
use App\Entity\Teams;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class createTeamTest extends KernelTestCase
{
    private $entityManager;

    public function setUp()
    {
        self::bootKernel();
        $container = self::$container;

        $this->entityManager = $container
            ->get('doctrine')
            ->getManager();
    }

    public function test_de_creation_de_team()
    {
        $equipeService = self::$container->get('App\Service\EquipeService');

        $equipeService->createTeam('equipe Creation Test',1,$this->entityManager->getRepository(Races::class)->findOneBy(['raceId' => 9]));

        $this->assertEquals(1,count($this->entityManager->getRepository(Teams::class)->findOneBy(['name' => 'equipe Creation Test'])));
    }

    public function tearDown()
    {
        $equipe = $this->entityManager->getRepository(Teams::class)->findOneBy(['name' => 'equipe Creation Test']);
        $this->entityManager->remove($equipe);

        $this->entityManager->flush();
    }
}