<?php

namespace App\Tests\src\Service\EquipeService;


use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AjoutInducement extends KernelTestCase
{
    public function le_cout_des_rr()
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $products = $this->entityManager
            ->getRepository(Teams::class)
            ->findBy(['name'=>'black fumble']);

        $this->assertCount(1, $products);
    }

}