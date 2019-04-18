<?php


namespace App\Tests\src\Service\EquipeService;

use App\Entity\Teams;
use App\Service\EquipeService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class eloDesEquipesTest extends KernelTestCase
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
    public function testElo()
    {
        /** @var EquipeService $service */
        $equipeService = self::$container->get('App\Service\EquipeService');

        $equipeCollection =  $this->entityManager->getRepository(Teams::class)->findBy(['year' => 3]);

        $retour = $equipeService->eloDesEquipes(3);

        foreach ($equipeCollection as $equipe) {
            $listeEquipe[$equipe->getTeamId()] = $equipe->getElo();
        }

        //$this->assertEquals($listeEquipe,$retour);
    }
}