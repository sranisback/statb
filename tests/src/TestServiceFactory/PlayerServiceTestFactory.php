<?php


namespace App\Tests\src\TestServiceFactory;


use App\Service\InfosService;
use App\Service\MatchDataService;
use App\Service\PlayerService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class PlayerServiceTestFactory extends TestCase
{
    public function getInstance(
        EntityManagerInterface $doctrineEntityManager = NULL,
        MatchDataService $matchDataService = NULL,
        InfosService $infosService = NULL
    ): PlayerService {
        return new PlayerService(
            $doctrineEntityManager == null ? $this->createMock(EntityManagerInterface::class) : $doctrineEntityManager,
            $matchDataService == null ? $this->createMock(MatchDataService::class) : $matchDataService,
            $infosService == null ?$this->createMock(InfosService::class) : $infosService
        );
    }
}