<?php

namespace App\Tests\src\Service\MatchesService;

use App\Entity\Coaches;
use App\Entity\Matches;
use App\Entity\Teams;
use App\Service\DefisService;
use App\Service\EquipeGestionService;
use App\Service\EquipeService;
use App\Service\InfosService;
use App\Service\MatchesService;
use App\Service\PlayerService;
use App\Service\SettingsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class tousLesMatchesDunCoachParAnneeTest extends KernelTestCase
{
    /**
     * @test
     */
    public function un_match_par_equipe_par_an(): void
    {
        $coachMock = $this->createMock(Coaches::class);
        $coachMock->method('getCoachId')->willReturn(0);

        $equipeMock0 = $this->createMock(Teams::class);
        $equipeMock0->method('getYear')->willReturn(0);

        $equipeMock1 = $this->createMock(Teams::class);
        $equipeMock1->method('getYear')->willReturn(1);

        $equipeMock2 = $this->createMock(Teams::class);
        $equipeMock2->method('getYear')->willReturn(2);

        $equipeMock3 = $this->createMock(Teams::class);
        $equipeMock3->method('getYear')->willReturn(3);

        $matchMock0 = $this->createMock(Matches::class);
        $matchMock1 = $this->createMock(Matches::class);
        $matchMock2 = $this->createMock(Matches::class);
        $matchMock3 = $this->createMock(Matches::class);
        $matchMock4 = $this->createMock(Matches::class);

        $equipeRepoMock = $this->getMockBuilder(Teams::class)
            ->addMethods(['toutesLesEquipesDunCoachParAnnee'])
            ->getMock();

        $equipeRepoMock->method('toutesLesEquipesDunCoachParAnnee')->willReturnOnConsecutiveCalls(
            [$equipeMock0],
            [$equipeMock1],
            [$equipeMock2],
            [$equipeMock3]
        );

        $matchRepoMock = $this->getMockBuilder(Matches::class)
            ->addMethods(['listeDesMatchs'])
            ->getMock();

        $matchRepoMock->method('listeDesMatchs')->willReturnOnConsecutiveCalls(
            [$matchMock0],
            [$matchMock1],
            [$matchMock2],
            [$matchMock3]
        );

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->will(
            $this->returnCallback(
                function ($entityName) use ($equipeRepoMock, $matchRepoMock) {
                    if ($entityName === 'App\Entity\Teams') {
                        return $equipeRepoMock;
                    }

                    if ($entityName === 'App\Entity\Matches') {
                        return $matchRepoMock;
                    }

                    return true;
                }
            )
        );

        $settingServiceMock = $this->createMock(SettingsService::class);
        $settingServiceMock->expects($this->any())->method('anneeCourante')->willReturn(4);

        $matchServiceTest = new MatchesService(
            $objectManager,
            $this->createMock(EquipeService::class),
            $this->createMock(PlayerService::class),
            $settingServiceMock,
            $this->createMock(DefisService::class),
            $this->createMock(InfosService::class),
            $this->createMock(EquipeGestionService::class)
        );

        $retourAttendu = [
            "2015 - 2016" => [$matchMock0],
            "2016 - 2017" => [$matchMock1],
            "2017 - 2018" => [$matchMock3],
            "2018 - 2019" => [$matchMock4]
        ];

        $test = $matchServiceTest->tousLesMatchesDunCoachParAnnee($coachMock);

        $this->assertEquals($retourAttendu, $test);
    }

    /**
     * @test
     */
    public function deux_equipes_deux_match_par_an(): void
    {
        $coachMock = $this->createMock(Coaches::class);
        $coachMock->method('getCoachId')->willReturn(0);

        $equipeMock0 = $this->createMock(Teams::class);
        $equipeMock0->method('getYear')->willReturn(0);

        $equipeMock1 = $this->createMock(Teams::class);
        $equipeMock1->method('getYear')->willReturn(1);

        $matchMock0 = $this->createMock(Matches::class);
        $matchMock1 = $this->createMock(Matches::class);
        $matchMock2 = $this->createMock(Matches::class);
        $matchMock3 = $this->createMock(Matches::class);

        $equipeRepoMock = $this->getMockBuilder(Teams::class)
            ->addMethods(['toutesLesEquipesDunCoachParAnnee'])
            ->getMock();

        $equipeRepoMock->method('toutesLesEquipesDunCoachParAnnee')->willReturnOnConsecutiveCalls(
            [$equipeMock0],
            [$equipeMock1],
            [],
            []
        );

        $matchRepoMock = $this->getMockBuilder(Matches::class)
            ->addMethods(['listeDesMatchs'])
            ->getMock();

        $matchRepoMock->method('listeDesMatchs')->willReturnOnConsecutiveCalls(
            [$matchMock0, $matchMock1],
            [$matchMock2, $matchMock3],
            [],
            []
        );

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->will(
            $this->returnCallback(
                function ($entityName) use ($equipeRepoMock, $matchRepoMock) {
                    if ($entityName === 'App\Entity\Teams') {
                        return $equipeRepoMock;
                    }

                    if ($entityName === 'App\Entity\Matches') {
                        return $matchRepoMock;
                    }

                    return true;
                }
            )
        );

        $settingServiceMock = $this->createMock(SettingsService::class);
        $settingServiceMock->expects($this->any())->method('anneeCourante')->willReturn(4);

        $matchServiceTest = new MatchesService(
            $objectManager,
            $this->createMock(EquipeService::class),
            $this->createMock(PlayerService::class),
            $settingServiceMock,
            $this->createMock(DefisService::class),
            $this->createMock(InfosService::class),
            $this->createMock(EquipeGestionService::class)
        );

        $retourAttendu = [
            "2015 - 2016" => [$matchMock0, $matchMock1],
            "2016 - 2017" => [$matchMock2, $matchMock3],
            "2017 - 2018" => [],
            "2018 - 2019" => []
        ];

        $test = $matchServiceTest->tousLesMatchesDunCoachParAnnee($coachMock);

        $this->assertEquals($retourAttendu, $test);
    }

    /**
     * @test
     */
    public function une_annee_est_vide(): void
    {
        $coachMock = $this->createMock(Coaches::class);
        $coachMock->method('getCoachId')->willReturn(0);

        $equipeMock0 = $this->createMock(Teams::class);
        $equipeMock0->method('getYear')->willReturn(0);

        $equipeMock1 = $this->createMock(Teams::class);
        $equipeMock1->method('getYear')->willReturn(2);

        $matchMock0 = $this->createMock(Matches::class);
        $matchMock1 = $this->createMock(Matches::class);
        $matchMock2 = $this->createMock(Matches::class);
        $matchMock3 = $this->createMock(Matches::class);

        $equipeRepoMock = $this->getMockBuilder(Teams::class)
            ->addMethods(['toutesLesEquipesDunCoachParAnnee'])
            ->getMock();

        $equipeRepoMock->method('toutesLesEquipesDunCoachParAnnee')->willReturnOnConsecutiveCalls(
            [$equipeMock0],
            [],
            [$equipeMock1],
            []
        );

        $matchRepoMock = $this->getMockBuilder(Matches::class)
            ->addMethods(['listeDesMatchs'])
            ->getMock();

        $matchRepoMock->method('listeDesMatchs')->willReturnOnConsecutiveCalls(
            [$matchMock0, $matchMock1],
            [$matchMock2, $matchMock3]
        );

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->will(
            $this->returnCallback(
                function ($entityName) use ($equipeRepoMock, $matchRepoMock) {
                    if ($entityName === 'App\Entity\Teams') {
                        return $equipeRepoMock;
                    }

                    if ($entityName === 'App\Entity\Matches') {
                        return $matchRepoMock;
                    }

                    return true;
                }
            )
        );

        $settingServiceMock = $this->createMock(SettingsService::class);
        $settingServiceMock->expects($this->any())->method('anneeCourante')->willReturn(4);

        $matchServiceTest = new MatchesService(
            $objectManager,
            $this->createMock(EquipeService::class),
            $this->createMock(PlayerService::class),
            $settingServiceMock,
            $this->createMock(DefisService::class),
            $this->createMock(InfosService::class),
            $this->createMock(EquipeGestionService::class)
        );

        $retourAttendu = [
            "2015 - 2016" => [$matchMock0, $matchMock1],
            "2016 - 2017" => [],
            "2017 - 2018" => [$matchMock2, $matchMock3],
            "2018 - 2019" => []
        ];

        $test = $matchServiceTest->tousLesMatchesDunCoachParAnnee($coachMock);

        $this->assertEquals($retourAttendu, $test);
    }

    /**
     * @test
     */
    public function il_n_y_a_pas_de_donnees(): void
    {
        $coachMock = $this->createMock(Coaches::class);
        $coachMock->method('getCoachId')->willReturn(0);

        $equipeRepoMock = $this->getMockBuilder(Teams::class)
            ->addMethods(['toutesLesEquipesDunCoachParAnnee'])
            ->getMock();

        $equipeRepoMock->method('toutesLesEquipesDunCoachParAnnee')->willReturn(
            []
        );

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($equipeRepoMock);

        $settingServiceMock = $this->createMock(SettingsService::class);
        $settingServiceMock->expects($this->any())->method('anneeCourante')->willReturn(4);

        $matchServiceTest = new MatchesService(
            $objectManager,
            $this->createMock(EquipeService::class),
            $this->createMock(PlayerService::class),
            $settingServiceMock,
            $this->createMock(DefisService::class),
            $this->createMock(InfosService::class),
            $this->createMock(EquipeGestionService::class)
        );

        $retourAttendu = [
            "2015 - 2016" => [],
            "2016 - 2017" => [],
            "2017 - 2018" => [],
            "2018 - 2019" => []
        ];

        $test = $matchServiceTest->tousLesMatchesDunCoachParAnnee($coachMock);

        $this->assertEquals($retourAttendu, $test);
    }

    /**
     * @test
     */
    public function il_y_a_plusieur_equipes_dans_une_annee(): void
    {
        $coachMock = $this->createMock(Coaches::class);
        $coachMock->method('getCoachId')->willReturn(0);

        $equipeMock0 = $this->createMock(Teams::class);
        $equipeMock0->method('getYear')->willReturn(0);

        $equipeMock1 = $this->createMock(Teams::class);
        $equipeMock1->method('getYear')->willReturn(1);

        $equipeMock2 = $this->createMock(Teams::class);
        $equipeMock2->method('getYear')->willReturn(2);

        $equipeMock3 = $this->createMock(Teams::class);
        $equipeMock3->method('getYear')->willReturn(3);

        $matchMock0 = $this->createMock(Matches::class);
        $matchMock1 = $this->createMock(Matches::class);
        $matchMock2 = $this->createMock(Matches::class);
        $matchMock3 = $this->createMock(Matches::class);
        $matchMock4 = $this->createMock(Matches::class);

        $equipeRepoMock = $this->getMockBuilder(Teams::class)
            ->setMethods(['toutesLesEquipesDunCoachParAnnee'])
            ->getMock();

        $equipeRepoMock->method('toutesLesEquipesDunCoachParAnnee')->willReturnOnConsecutiveCalls(
            [$equipeMock0,$equipeMock1],
            [],
            [$equipeMock2],
            [$equipeMock3]
        );

        $matchRepoMock = $this->getMockBuilder(Matches::class)
            ->setMethods(['listeDesMatchs'])
            ->getMock();

        $matchRepoMock->method('listeDesMatchs')->willReturnOnConsecutiveCalls(
            [$matchMock0],
            [$matchMock1],
            [$matchMock2],
            [$matchMock3]
        );

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->will(
            $this->returnCallback(
                function ($entityName) use ($equipeRepoMock, $matchRepoMock) {
                    if ($entityName === 'App\Entity\Teams') {
                        return $equipeRepoMock;
                    }

                    if ($entityName === 'App\Entity\Matches') {
                        return $matchRepoMock;
                    }

                    return true;
                }
            )
        );

        $settingServiceMock = $this->createMock(SettingsService::class);
        $settingServiceMock->expects($this->any())->method('anneeCourante')->willReturn(4);

        $matchServiceTest = new MatchesService(
            $objectManager,
            $this->createMock(EquipeService::class),
            $this->createMock(PlayerService::class),
            $settingServiceMock,
            $this->createMock(DefisService::class),
            $this->createMock(InfosService::class),
            $this->createMock(EquipeGestionService::class)
        );

        $retourAttendu = [
            "2015 - 2016" => [$matchMock0,$matchMock1],
            "2016 - 2017" => [],
            "2017 - 2018" => [$matchMock3],
            "2018 - 2019" => [$matchMock4]
        ];

        $test = $matchServiceTest->tousLesMatchesDunCoachParAnnee($coachMock);

        $this->assertEquals($retourAttendu, $test);
    }
}
