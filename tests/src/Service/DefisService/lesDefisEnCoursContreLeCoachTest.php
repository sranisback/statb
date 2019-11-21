<?php

namespace App\Tests\src\Service\DefisService;


use App\Entity\Coaches;
use App\Entity\Defis;
use App\Entity\Teams;
use App\Service\DefisService;
use App\Service\SettingsService;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class lesDefisEnCoursContreLeCoachTest extends KernelTestCase
{
    /**
     * @test
     */
    public function la_liste_des_defis_en_cours_contre_un_coach_est_retournee()
    {
        $settingServiceMock = $this->createMock(SettingsService::class);
        $settingServiceMock->method('anneeCourante')->willReturn(3);

        $coachMock = $this->createMock(Coaches::class);
        $coachMock->method('getCoachId')->willReturn(0);

        $equipeMock0 = $this->createMock(Teams::class);
        $equipeMock0->method('getName')->willReturn('titi');
        $equipeMock1 = $this->createMock(Teams::class);
        $equipeMock2 = $this->createMock(Teams::class);
        $equipeMock2->method('getName')->willReturn('coco');
        $equipeMockCollection = [$equipeMock0, $equipeMock1, $equipeMock2];

        $equipeAdverseMock0 = $this->createMock(Teams::class);
        $equipeAdverseMock0->method('getName')->willReturn('toto');
        $equipeAdverseMock1 = $this->createMock(Teams::class);
        $equipeAdverseMock1->method('getName')->willReturn('zozo');

        $defiMock0 = $this->createMock(Defis::class);
        $defiMock0->method('getEquipeOrigine')->willReturn($equipeAdverseMock0);
        $defiMock1 = $this->createMock(Defis::class);
        $defiMock1->method('getEquipeOrigine')->willReturn($equipeAdverseMock1);

        $teamRepoMock = $this->createMock(ObjectRepository::class);
        $teamRepoMock->method('findBy')->willReturn($equipeMockCollection);

        $defisRepoMock = $this->createMock(ObjectRepository::class);
        $defisRepoMock->method('findBy')->willReturnOnConsecutiveCalls([$defiMock0], [], [$defiMock1]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->will($this->returnCallback(
            function ($entityName) use ($teamRepoMock, $defisRepoMock) {
                if ($entityName === 'App\Entity\Teams') {
                    return $teamRepoMock;
                }

                if ($entityName === 'App\Entity\Defis') {
                    return $defisRepoMock;
                }
                return true;
            }
        ));

        $reponse = [['defiee' => 'titi', 'par' => 'toto'],['defiee' => 'coco', 'par' => 'zozo']];

        $defisService = new DefisService($objectManager);
        $this->assertEquals($reponse, $defisService->lesDefisEnCoursContreLeCoach($settingServiceMock, $coachMock));
    }
}