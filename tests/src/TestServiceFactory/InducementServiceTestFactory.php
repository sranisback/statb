<?php


namespace App\Tests\src\TestServiceFactory;

use App\Service\InducementService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class InducementServiceTestFactory extends TestCase
{
    public function getInstance(
        EntityManagerInterface $doctrineEntityManager = NULL
    ): InducementService {
        return new InducementService(
            $doctrineEntityManager == null ? $this->createMock(EntityManagerInterface::class) : $doctrineEntityManager,
        );
    }
}