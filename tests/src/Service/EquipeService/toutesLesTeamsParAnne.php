<?php

namespace App\Tests\src\Service\EquipeService;

use App\Entity\Teams;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class toutesLesTeamsParAnne extends KernelTestCase
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
     * /*@test
     */
    public function liste_des_equipe_de_l_anne_en_table()
    {
        $equipeService = self::$container->get('App\Service\EquipeService');
    }
}

