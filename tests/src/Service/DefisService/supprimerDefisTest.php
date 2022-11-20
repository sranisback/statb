<?php


namespace App\Tests\src\Service\DefisService;


use App\Entity\Defis;
use App\Service\DefisService;
use App\Service\InfosService;
use App\Service\SettingsService;
use App\Tests\src\TestServiceFactory\DefisServiceTestFactory;
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

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->expects($this->once())->method('remove')->with($defis);
        $objectManager->expects($this->once())->method('flush');

        $defiService = (new DefisServiceTestFactory)->getInstance(
            $objectManager
        );

        $defiService->supprimerDefis($defis);
    }
}