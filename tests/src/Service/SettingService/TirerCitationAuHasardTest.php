<?php


namespace App\Tests\src\Service\SettingService;


use App\Entity\Citations;
use App\Entity\Coaches;
use App\Service\SettingsService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;

class TirerCitationAuHasardTest extends TestCase
{
    /**
     * @test
     */
    public function il_n_y_a_pas_de_citations()
    {
        $settingRepoMock = $this->createMock(ObjectRepository::class);
        $settingRepoMock->method('findOneBy')->willReturn(null);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($settingRepoMock);

        $settingsService = new SettingsService($objectManager);

        $this->assertEquals(new Citations(),$settingsService->tirerCitationAuHasard());
    }

    /**
     * @test
     */
    public function un_objet_citation_est_retourne()
    {
        $citation = $this->createMock(Citations::class);

        $settingRepoMock = $this->createMock(ObjectRepository::class);
        $settingRepoMock->method('findOneBy')->willReturn($citation);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($settingRepoMock);

        $settingsService = new SettingsService($objectManager);

        $this->assertEquals(new Citations(),$settingsService->tirerCitationAuHasard());
    }
}