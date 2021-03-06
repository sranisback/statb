<?php

namespace App\Tests\src\Service\SettingService;

use App\Entity\Setting;
use App\Service\SettingsService;
use DateTime;
use Doctrine\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class periodeDefisCourranteTest extends TestCase
{
    /**
     * @test
     */
    public function la_fin_de_la_periode_correcte_est_retournee(): void
    {
        $setting = new Setting();
        $setting->setName('periodeDefis');
        $setting->setValue('07/25/2019');

        $settingRepoMock = $this->createMock(ObjectRepository::class);
        $settingRepoMock->expects($this->any())->method('findOneBy')->willReturn($setting);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->expects($this->any())->method('getRepository')->willReturn($settingRepoMock);

        $settingsService = new SettingsService($objectManager);

        $periode = $settingsService->periodeDefisCourrante();

        $this->assertEquals(
            DateTime::createFromFormat("d/m/Y",date('d/m/Y', strtotime($setting->getValue().'+2 months'))),
            $periode['fin']
        );
    }

    /**
     * @test
     */
    public function il_n_y_a_pas_de_periode_configuree()
    {
        $settingRepoMock = $this->createMock(ObjectRepository::class);
        $settingRepoMock->expects($this->any())->method('findOneBy')->willReturn(null);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->expects($this->any())->method('getRepository')->willReturn($settingRepoMock);

        $settingsService = new SettingsService($objectManager);

        $this->assertEmpty($settingsService->periodeDefisCourrante());
    }
}