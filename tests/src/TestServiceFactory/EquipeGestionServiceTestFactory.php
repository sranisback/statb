<?php


namespace App\Tests\src\TestServiceFactory;


use App\Service\EquipeGestionService;
use App\Service\InducementService;
use App\Service\InfosService;
use App\Service\PlayerService;
use App\Service\SettingsService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class EquipeGestionServiceTestFactory extends TestCase
{
    public function getInstance(
        EntityManagerInterface $doctrineEntityManager = NULL,
        SettingsService $settingsService = NULL,
        InfosService $infoService = NULL,
        InducementService $inducementService = NULL,
        PlayerService $playerService = NULL
    ) {
        return new EquipeGestionService(
            $doctrineEntityManager == null ? $this->createMock(EntityManagerInterface::class) : $doctrineEntityManager,
            $settingsService == null ? $this->createMock(SettingsService::class) : $settingsService,
            $infoService == null ?$this->createMock(InfosService::class) : $infoService,
            $inducementService == null ?$this->createMock(InducementService::class) : $inducementService,
            $playerService == null ?$this->createMock(PlayerService::class) : $playerService
        );
    }

}