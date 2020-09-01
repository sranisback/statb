<?php


namespace App\Tests\src\Service\SettingService;

use App\Entity\Setting;
use App\Service\SettingsService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class pointEnCoursTest extends TestCase
{
    /**
     * @test
     */
    public function un_tableau_de_point_est_retourne_en_fonction_de_l_annee()
    {
        $settingRepoMock = $this->getMockBuilder(Setting::class)
            ->setMethods(['findByName'])->getMock();

        $settingRepoMock->method('findByName')->with(
            $this->logicalOr(
            'points_0',
            'points_6'
        )
        )->willReturnOnConsecutiveCalls('10;5;-4', '8;3;-3');

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($settingRepoMock);

        $settingsService = new SettingsService($objectManager);

        $this->assertEquals([10, 5, -4], $settingsService->pointsEnCours(0));
        $this->assertEquals([8, 3, -3], $settingsService->pointsEnCours(6));
    }
}
