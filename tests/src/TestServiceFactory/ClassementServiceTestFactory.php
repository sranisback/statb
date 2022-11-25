<?php


namespace App\Tests\src\TestServiceFactory;


use App\Service\ClassementService;
use App\Service\EquipeService;
use App\Service\MatchDataService;
use App\Service\SettingsService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class ClassementServiceTestFactory extends TestCase
{
    public function getInstance(
        EntityManagerInterface $doctrineEntityManager = NULL,
        EquipeService $equipeService = NULL,
        MatchDataService $matchDataService = NULL,
        SettingsService $settingsService = NULL
    ): ClassementService {
        return new ClassementService(
            $doctrineEntityManager == null ? $this->createMock(EntityManagerInterface::class) : $doctrineEntityManager,
            $equipeService == null ?$this->createMock(EquipeService::class) : $equipeService,
            $matchDataService == null ? $this->createMock(MatchDataService::class) : $matchDataService,
            $settingsService == null ? $this->createMock(SettingsService::class) : $settingsService
        );
    }
}