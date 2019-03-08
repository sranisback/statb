<?php

namespace App\Tests\src\Service\EquipeService;


use App\Entity\Teams;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Service\EquipeService;

class ClassementGeneral extends KernelTestCase
{

    /**
     * @test
     */
    public function que_retourne_le_repo_class_gen()
    {
        self::bootKernel();

        $container  = self::$kernel->getContainer();

        $container  = self::$container;

        /** @var $user $service */
        $user = self::$container->get('App\Service\EquipeService');
    }
}