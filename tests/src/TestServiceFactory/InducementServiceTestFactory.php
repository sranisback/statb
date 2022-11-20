<?php


namespace App\Tests\src\TestServiceFactory;


use App\Service\EquipeGestionService;
use App\Service\InducementService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class InducementServiceTestFactory extends TestCase
{
    public function getInstance(
        EntityManagerInterface $doctrineEntityManager = NULL,
        EquipeGestionService $equipeGestionService = NULL
    ): InducementService {
        return new InducementService(
            $doctrineEntityManager == null ? $this->createMock(EntityManagerInterface::class) : $doctrineEntityManager,
            $equipeGestionService == null ?$this->createMock(EquipeGestionService::class) : $equipeGestionService,
        );
    }
}