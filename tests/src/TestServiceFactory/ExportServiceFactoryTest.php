<?php


namespace App\Tests\src\TestServiceFactory;


use App\Service\EquipeGestionService;
use App\Service\EquipeService;
use App\Service\ExportService;
use App\Service\InducementService;
use App\Service\PlayerService;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Test\TestCase;

class ExportServiceFactoryTest extends TestCase
{
    public function getInstance(
        EntityManagerInterface $doctrineEntityManager = NULL,
        PlayerService $playerService = NULL,
        InducementService $inducementService = NULL,
        EquipeGestionService $equipeGestionService = NULL,
        EquipeService $equipeService = NULL
    ): ExportService {
        return new ExportService(
            $doctrineEntityManager == null ? $this->createMock(EntityManagerInterface::class) : $doctrineEntityManager,
            $playerService == null ?$this->createMock(PlayerService::class) : $playerService,
            $inducementService == null ?$this->createMock(InducementService::class) : $inducementService,
            $equipeGestionService == null ?$this->createMock(EquipeGestionService::class) : $equipeGestionService,
            $equipeService == null ?$this->createMock(EquipeService::class) : $equipeService
        );
    }
}