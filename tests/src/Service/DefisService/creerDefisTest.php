<?php


namespace App\Tests\src\Service\DefisService;


use App\Entity\Defis;
use App\Service\DefisService;
use App\Service\InfosService;
use App\Service\SettingsService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class creerDefisTest extends TestCase
{
    /**
     * @test
     */
    public function le_defis_est_cree()
    {
        $defis = new Defis();

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->expects($this->once())->method('persist')->with($defis);
        $objectManager->expects($this->once())->method('flush');

        $infoService = $this->createMock(InfosService::class);
        $infoService->expects($this->once())->method('defisEstLance')->with($defis);

        $defiService = new DefisService($objectManager, $infoService, $this->createMock(SettingsService::class));

        $defiService->creerDefis($defis);
    }
}