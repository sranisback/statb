<?php

namespace App\Tests\src\Service\EquipeService;


use App\Entity\Coaches;
use App\Entity\Teams;
use App\Service\EquipeService;
use App\Service\InfosService;
use App\Service\SettingsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class listeDesAnciennesEquipesTest extends KernelTestCase
{
    /**
     * @test
     */
    public function une_liste_d_ancienne_equipe_est_retournee(): void
    {
        $coach = $this->createMock(Coaches::class);

        $equipeMock0 = $this->createMock(Teams::class);
        $equipeMock0->method('getYear')->willReturn(0);

        $equipeMock1a = $this->createMock(Teams::class);
        $equipeMock1a->method('getYear')->willReturn(1);

        $equipeMock1b = $this->createMock(Teams::class);
        $equipeMock1b->method('getYear')->willReturn(1);

        $equipeMock2 = $this->createMock(Teams::class);
        $equipeMock2->method('getYear')->willReturn(2);

        $equipeMock3 = $this->createMock(Teams::class);
        $equipeMock3->method('getYear')->willReturn(3);

        $equipeRepoMock = $this->getMockBuilder(Teams::class)
            ->setMethods(['toutesLesEquipesDunCoachParAnnee'])
            ->getMock();

        $equipeRepoMock->method('toutesLesEquipesDunCoachParAnnee')->willReturnOnConsecutiveCalls(
            [$equipeMock0],
            [$equipeMock1a,$equipeMock1b],
            [$equipeMock2],
            [$equipeMock3]
        );

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($equipeRepoMock);

        $settingServiceMock = $this->createMock(SettingsService::class);
        $settingServiceMock->method('anneeCourante')->willReturn(3);

        $equipeService = new EquipeService(
            $objectManager,
            $settingServiceMock,
            $this->createMock(InfosService::class)
        );

        $this->assertEquals(4, count($equipeService->listeDesAnciennesEquipes($coach, 3)));
    }

    /**
     * @test
     */
    public function pas_d_ancienne_equipe(): void
    {
        $coach = $this->createMock(Coaches::class);

        $equipeRepoMock = $this->getMockBuilder(Teams::class)
            ->addMethods(['toutesLesEquipesDunCoachParAnnee'])
            ->getMock();

        $equipeRepoMock->method('toutesLesEquipesDunCoachParAnnee')->willReturn([]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($equipeRepoMock);

        $settingServiceMock = $this->createMock(SettingsService::class);
        $settingServiceMock->method('anneeCourante')->willReturn(3);

        $equipeService = new EquipeService(
            $objectManager,
            $settingServiceMock ,
            $this->createMock(InfosService::class)
        );

        $this->assertEquals(0, count($equipeService->listeDesAnciennesEquipes($coach, 3)));
    }

    /**
     * @test
     */
    public function une_liste_d_ancienne_equipe_est_retournee_avec_des_annees_vides(): void
    {
        $coach = $this->createMock(Coaches::class);

        $equipeMock0 = $this->createMock(Teams::class);
        $equipeMock0->method('getYear')->willReturn(0);

        $equipeMock1a = $this->createMock(Teams::class);
        $equipeMock1a->method('getYear')->willReturn(1);

        $equipeMock1b = $this->createMock(Teams::class);
        $equipeMock1b->method('getYear')->willReturn(1);

        $equipeMock3 = $this->createMock(Teams::class);
        $equipeMock3->method('getYear')->willReturn(3);

        $equipeRepoMock = $this->getMockBuilder(Teams::class)
            ->addMethods(['toutesLesEquipesDunCoachParAnnee'])
            ->getMock();

        $equipeRepoMock->method('toutesLesEquipesDunCoachParAnnee')->willReturnOnConsecutiveCalls(
            [$equipeMock0],
            [$equipeMock1a,$equipeMock1b],
            [],
            [$equipeMock3]
        );

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($equipeRepoMock);

        $settingServiceMock = $this->createMock(SettingsService::class);
        $settingServiceMock->method('anneeCourante')->willReturn(3);

        $equipeService = new EquipeService(
            $objectManager,
            $settingServiceMock,
            $this->createMock(InfosService::class)
        );

        $this->assertEquals(3, count($equipeService->listeDesAnciennesEquipes($coach, 3)));
    }
}