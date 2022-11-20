<?php


namespace App\Tests\src\TestServiceFactory;


use App\Service\ClassementService;
use App\Service\DefisService;
use App\Service\EquipeService;
use App\Service\InfosService;
use App\Service\MatchDataService;
use App\Service\SettingsService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class DefisServiceTestFactory extends TestCase
{
    public function getInstance(
        EntityManagerInterface $doctrineEntityManager = NULL,
        InfosService $infoService = NULL,
        SettingsService $settingsService = NULL
    ): DefisService {
        return new DefisService(
            $doctrineEntityManager == null ? $this->createMock(EntityManagerInterface::class) : $doctrineEntityManager,
            $infoService == null ?$this->createMock(InfosService::class) : $infoService,
            $settingsService == null ? $this->createMock(SettingsService::class) : $settingsService
        );
    }
}