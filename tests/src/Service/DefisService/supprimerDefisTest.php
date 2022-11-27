<?php


namespace App\Tests\src\Service\DefisService;


use App\Entity\Defis;
use App\Service\DefisService;
use App\Service\InfosService;
use App\Service\SettingsService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;

class supprimerDefisTest extends TestCase
{
    /**
     * @test
     */
    public function le_defis_est_supprime()
    {
        $defis = new Defis();

        $defiRepo = $this->createMock(ObjectRepository::class);
        $defiRepo->method('findOneBy')->willReturn($defis);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($defiRepo);
        $objectManager->expects($this->once())->method('remove')->with($defis);
        $objectManager->expects($this->once())->method('flush');

        $defiService = new DefisService(
            $objectManager,
            $this->createMock(InfosService::class),
            $this->createMock(SettingsService::class)
        );

        $defiService->supprimerDefis($defis);
    }
}