<?php

namespace App\Tests\src\Service\EquipeService;

use App\Entity\Matches;
use App\Entity\Teams;
use App\Service\EquipeService;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class listeDesMatches extends KernelTestCase
{

    /**
     * @test
     */
    public function test()
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