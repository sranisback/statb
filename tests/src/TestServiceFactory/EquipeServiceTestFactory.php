<?php


namespace App\Tests\src\TestServiceFactory;


use App\Service\EquipeGestionService;
use App\Service\EquipeService;
use App\Service\InducementService;
use App\Service\PlayerService;
use App\Service\SettingsService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class EquipeServiceTestFactory extends TestCase
{
    public function getInstance(
        EntityManagerInterface $doctrineEntityManager = NULL,
        SettingsService $settingsService = NULL,
        InducementService $inducementService = NULL,
        EquipeGestionService $equipeGestionService = NULL,
        PlayerService $playerService = NULL
    ): EquipeService {
        return new EquipeService(
            $doctrineEntityManager == null ? $this->createMock(EntityManagerInterface::class) : $doctrineEntityManager,
            $settingsService == null ? $this->createMock(SettingsService::class) : $settingsService,
            $inducementService == null ?$this->createMock(InducementService::class) : $inducementService,
            $equipeGestionService == null ?$this->createMock(EquipeGestionService::class) : $equipeGestionService,
            $playerService == null ?$this->createMock(PlayerService::class) : $playerService
        );
    }
}