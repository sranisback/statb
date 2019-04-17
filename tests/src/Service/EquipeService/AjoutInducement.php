<?php

namespace App\src\Service\EquipeService;

use App\Entity\Teams;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AjoutInducement extends KernelTestCase
{
    /**
     * @test
     */
    public function le_cout_des_rr()
    {
        $kernel = self::bootKernel();

        $entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $products = $entityManager
            ->getRepository(Teams::class)
            ->findBy(['name'=>'black fumble']);

        $this->assertCount(1, $products);
    }

}