<?php


namespace App\Tests\src\Service\EquipeService;

use App\Entity\Teams;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Service\EquipeService;

class CalculPointsBonus extends KernelTestCase
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
    public function les_pts_bonus_sont_biens_calcules()
    {
        $equipeService = self::$container->get('App\Service\EquipeService');

        $EquipePourTest = $this->entityManager
            ->getRepository(Teams::class)->findOneBy(['name'=>'Les Ratgwents']);

        $this->assertEquals(1,$equipeService->calculPointsBonus($EquipePourTest));
    }
}