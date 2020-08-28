<?php

namespace App\Tests\src\Service\SettingService;


use App\Entity\Setting;
use App\Service\SettingsService;
use DateTime;
use Doctrine\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class dateDansLaPeriodeCouranteTest extends TestCase
{
    /**
     * @test
     */
    public function la_date_est_dans_la_periode_ciblee(): void
    {
        $setting = new Setting();
        $setting->setName('periodeDefis');
        $setting->setValue('07/25/2019');

        $settingRepoMock = $this->createMock(ObjectRepository::class);
        $settingRepoMock->expects($this->any())->method('findOneBy')->willReturn($setting);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->expects($this->any())->method('getRepository')->willReturn($settingRepoMock);

        $settingsService = new SettingsService($objectManager);

        $this->assertTrue(
            $settingsService->dateDansLaPeriodeCourante(DateTime::createFromFormat("d/m/Y", '25/08/2019'))
        );
    }

    /**
     * @test
     */
    public function la_date_est_pas_dans_la_periode_ciblee(): void
    {
        $setting = new Setting();
        $setting->setName('periodeDefis');
        $setting->setValue('07/25/2019');

        $settingRepoMock = $this->createMock(ObjectRepository::class);
        $settingRepoMock->expects($this->any())->method('findOneBy')->willReturn($setting);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->expects($this->any())->method('getRepository')->willReturn($settingRepoMock);

        $settingsService = new SettingsService($objectManager);

        $this->assertFalse($settingsService->dateDansLaPeriodeCourante(DateTime::createFromFormat("d/m/Y", '10/25/2019')));
    }
}