<?php

namespace App\Tests\src\Service\PlayerService;


use App\Entity\GameDataPlayers;
use App\Entity\GameDataSkills;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class listeDesCompdUnePositionTest extends KernelTestCase
{
    private $entityManager;

    protected function setUp()
    {
        self::bootKernel();
        $container = self::$container;

        $this->entityManager = $container
            ->get('doctrine')
            ->getManager();
    }

    /**
     * @test
     */
    public function toutes_les_comps_sont_retournees()
    {
        $playerService = self::$container->get('App\Service\PlayerService');

        $position = $this->entityManager->getRepository(GameDataPlayers::class)->findOneBy(['pos' => 'Witch Elf']);

        $retour = '<text class="test-primary">Frenzy</text>, <text class="test-primary">Dodge</text>, <text class="test-primary">Jump Up</text>, ';

        $this->assertEquals($playerService->listeDesCompdUnePosition($position),$retour);
    }

    protected function tearDown()
    {

    }
}