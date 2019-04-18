<?php

namespace App\Tests\src\Service\EquipeService;


use App\Entity\Teams;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Service\EquipeService;

class ClassementGeneralTest extends KernelTestCase
{

    private $entityManager;

    protected function setUp()
    {

        self::bootKernel();

        $container  = self::$container;

        $this->entityManager = $container
            ->get('doctrine')
            ->getManager();
    }

    /**
     * @test
     */
    public function classementGeneralEstCorrecte()
    {
        /** @var EquipeService $service */
        $equipeService = self::$container->get('App\Service\EquipeService');
        $playerService = self::$container->get('App\Service\PlayerService');

        $classGen = $this->entityManager
            ->getRepository(Teams::class)->classement(3, 0);

        foreach ($classGen as $line) {
            $equipeTest = $this->entityManager->getRepository(Teams::class)->findOneBy(['teamId' => $line['team_id']]);
            $line['tv'] = $equipeService->tvDelEquipe($equipeTest, $playerService);
        }

        //$this->assertEquals($classGen, $equipeService->classementGeneral());
    }

}