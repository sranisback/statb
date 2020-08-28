<?php

namespace App\Tests\src\Service\SettingService;


use App\Entity\Setting;
use App\Service\SettingsService;
use Doctrine\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class mettreaJourLaPeriodeTest extends TestCase
{
    /**
     * @test
     */
    public function la_periode_est_mise_a_jour()
    {
        $setting = new Setting();
        $setting->setName('periodeDefis');
        $setting->setValue('06/01/2019');

        $settingRepoMock = $this->createMock(ObjectRepository::class);
        $settingRepoMock->expects($this->any())->method('findOneBy')->willReturn($setting);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($settingRepoMock);

        $settingsService = new SettingsService($objectManager);

        $this->assertTrue($settingsService->mettreaJourLaPeriode('09/01/2019'));
    }
}