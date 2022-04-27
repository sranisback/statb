<?php


namespace App\Tests\src\Service\SettingService;

use App\Entity\Dyk;
use App\Service\SettingsService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class tirerDYKauHasardTest extends TestCase
{
    /**
     * @test
     */
    public function tirer_dyk_au_hasard()
    {
        $dykMock = $this->createMock(Dyk::class);
        $dykMock->method('getDykText')->willReturn('test');

        $dykRepoMock = $this->getMockBuilder(Dyk::class)
            ->addMethods(['findAll'])->getMock();

        $dykRepoMock->method('findAll')->willReturn([$dykMock, $dykMock, $dykMock]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($dykRepoMock);

        $settingServiceTest = new SettingsService(
            $objectManager
        );

        $this->assertEquals('<b>Did you know ?</b> <i>test</i>', $settingServiceTest->tirerDYKauHasard());
    }

    /**
     * @test
     */
    public function il_n_y_a_qu_un_dyk()
    {
        $dykMock = $this->createMock(Dyk::class);
        $dykMock->method('getDykText')->willReturn('test');

        $dykRepoMock = $this->getMockBuilder(Dyk::class)
            ->addMethods(['findAll'])->getMock();

        $dykRepoMock->method('findAll')->willReturn([$dykMock]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($dykRepoMock);

        $settingServiceTest = new SettingsService(
            $objectManager
        );

        $this->assertEquals('<b>Did you know ?</b> <i>test</i>', $settingServiceTest->tirerDYKauHasard());
    }

    /**
     * @test
     */
    public function il_n_y_a_pas_de_dyk()
    {
        $dykMock = $this->createMock(Dyk::class);
        $dykMock->method('getDykText')->willReturn('test');

        $dykRepoMock = $this->getMockBuilder(Dyk::class)
            ->addMethods(['findAll'])->getMock();

        $dykRepoMock->method('findAll')->willReturn(null);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($dykRepoMock);

        $settingServiceTest = new SettingsService(
            $objectManager
        );

        $this->assertEquals('<b>Did you know ?</b> <i>Dyk vide</i>', $settingServiceTest->tirerDYKauHasard());
    }
}
