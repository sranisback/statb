<?php

namespace App\Tests\src\Service\DefisService;


use App\Entity\Coaches;
use App\Entity\Defis;
use App\Entity\Teams;
use App\Service\DefisService;
use App\Service\SettingsService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;

class defiAutoriseTest extends TestCase
{
    /**
     * @test
     */
    public function le_defi_est_autorise()
    {
        $coach = $this->createMock(Coaches::class);
        $coach->method('getCoachId')->willReturn(1);

        $equipe0 = $this->createMock(Teams::class);
        $equipe0->method('getOwnedByCoach')->willReturn($coach);

        $equipe1 = $this->createMock(Teams::class);
        $equipe1->method('getOwnedByCoach')->willReturn($coach);

        $equipe2 = $this->createMock(Teams::class);
        $equipe2->method('getOwnedByCoach')->willReturn($coach);

        $equipe3 = $this->createMock(Teams::class);
        $equipe3->method('getOwnedByCoach')->willReturn($coach);

        $equipeCollection = [$equipe0, $equipe1, $equipe2, $equipe3];

        $teamMock = $this->createMock(Teams::class);
        $teamMock->method('getTeamId')->willReturn(1);

        $teamRepoMock = $this->createMock(ObjectRepository::class);
        $teamRepoMock->method('findBy')->willReturn($equipeCollection);

        $defiMock = $this->createMock(Defis::class);
        $defiMock->method('getDateDefi')->willReturn(\DateTime::createFromFormat('d/m/Y', '19/03/2019'));
        $defiMock2 = $this->createMock(Defis::class);
        $defiMock2->method('getDateDefi')->willReturn(\DateTime::createFromFormat('d/m/Y', '19/01/2019'));

        $defiRepoMock = $this->createMock(ObjectRepository::class);
        $defiRepoMock->expects($this->any())->method('findBy')->willReturn([$defiMock,$defiMock2]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->expects($this->any())->method('getRepository')->will($this->returnCallback(
            function ($entityName) use ($teamRepoMock, $defiRepoMock) {
                if ($entityName === 'App\Entity\Teams') {
                    return $teamRepoMock;
                }

                if ($entityName === 'App\Entity\Defis') {
                    return $defiRepoMock;
                }

                return true;
            }
        ));

        $periode = [
            'debut' => \DateTime::createFromFormat('d/m/Y', '01/07/2019'),
            'fin' => \DateTime::createFromFormat('d/m/Y', '01/09/2019'),
        ];
        $settingServiceMock = $this->createMock(SettingsService::class);
        $settingServiceMock->expects($this->any())->method('periodeDefisCourrante')->willReturn($periode);
        $settingServiceMock->expects($this->any())->method('anneeCourante')->willReturn(3);
        $settingServiceMock->expects($this->any())->method('dateDansLaPeriodeCourante')->willReturn(false);

        $defisService = new DefisService($objectManager);

        $this->assertTrue($defisService->defiAutorise($teamMock,$settingServiceMock));
    }

    /**
     * @test
     */
    public function le_defi_est_refuse()
    {
        $coach = $this->createMock(Coaches::class);
        $coach->method('getCoachId')->willReturn(1);

        $equipe0 = $this->createMock(Teams::class);
        $equipe0->method('getOwnedByCoach')->willReturn($coach);

        $equipe1 = $this->createMock(Teams::class);
        $equipe1->method('getOwnedByCoach')->willReturn($coach);

        $equipe2 = $this->createMock(Teams::class);
        $equipe2->method('getOwnedByCoach')->willReturn($coach);

        $equipe3 = $this->createMock(Teams::class);
        $equipe3->method('getOwnedByCoach')->willReturn($coach);

        $equipeCollection = [$equipe0, $equipe1, $equipe2, $equipe3];

        $teamMock = $this->createMock(Teams::class);
        $teamMock->method('getTeamId')->willReturn(1);

        $teamRepoMock = $this->createMock(ObjectRepository::class);
        $teamRepoMock->method('findBy')->willReturn($equipeCollection);

        $defiMock = $this->createMock(Defis::class);
        $defiMock->method('getDateDefi')->willReturn(\DateTime::createFromFormat('d/m/Y', '19/06/2019'));
        $defiMock2 = $this->createMock(Defis::class);
        $defiMock2->method('getDateDefi')->willReturn(\DateTime::createFromFormat('d/m/Y', '19/01/2019'));

        $defiRepoMock = $this->createMock(ObjectRepository::class);
        $defiRepoMock->expects($this->any())->method('findBy')->willReturn([$defiMock,$defiMock2]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->expects($this->any())->method('getRepository')->will($this->returnCallback(
            function ($entityName) use ($teamRepoMock, $defiRepoMock) {
                if ($entityName === 'App\Entity\Teams') {
                    return $teamRepoMock;
                }

                if ($entityName === 'App\Entity\Defis') {
                    return $defiRepoMock;
                }

                return true;
            }
        ));

        $periode = [
            'debut' => \DateTime::createFromFormat('d/m/Y', '01/07/2019'),
            'fin' => \DateTime::createFromFormat('d/m/Y', '01/09/2019'),
        ];

        $settingServiceMock = $this->createMock(SettingsService::class);
        $settingServiceMock->expects($this->any())->method('periodeDefisCourrante')->willReturn($periode);
        $settingServiceMock->expects($this->any())->method('anneeCourante')->willReturn(3);
        $settingServiceMock->expects($this->any())->method('dateDansLaPeriodeCourante')->willReturn(true);

        $defisService = new DefisService($objectManager);

        $this->assertFalse($defisService->defiAutorise($teamMock,$settingServiceMock));
    }
}