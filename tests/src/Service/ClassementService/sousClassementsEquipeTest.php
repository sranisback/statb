<?php

namespace App\Tests\src\Service\ClassementService;

use App\Entity\MatchData;
use App\Entity\Players;
use App\Entity\Setting;
use App\Entity\Teams;
use App\Enum\RulesetEnum;
use App\Service\ClassementService;
use App\Service\EquipeService;
use App\Service\MatchDataService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;

class sousClassementsEquipeTest extends TestCase
{
    /**
     * @test
     */
    public function le_classement_bash_est_retournee(): void
    {
        $teamMock0 = $this->createMock(Teams::class);
        $teamMock1 = $this->createMock(Teams::class);
        $teamMock2 = $this->createMock(Teams::class);
        $teamMock3 = $this->createMock(Teams::class);
        $teamMock4 = $this->createMock(Teams::class);

        $match_data_test = [
            'teams' => [$teamMock0, $teamMock1, $teamMock2, $teamMock3, $teamMock4],
            'title' => 'Les plus méchants',
            'class' => 'class_Tbash',
            'type' => 'bash',
            'limit' => 5,
            'annee' => 3
        ];

        $matchDataRepoMock = $this->getMockBuilder(MatchData::class)
            ->addMethods(['sousClassementEquipe'])
            ->getMock();

        $matchDataRepoMock->method('sousClassementEquipe')->willReturn(
            [$teamMock0, $teamMock1, $teamMock2, $teamMock3, $teamMock4]
        );


        $settingMock = $this->createMock(Setting::class);
        $settingMock->method('getValue')->willReturn((string) RulesetEnum::BB_2016);

        $settingRepoMock = $this->createMock(ObjectRepository::class);
        $settingRepoMock->method('findOneBy')->willReturn($settingMock);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->will(
            $this->returnCallback(
                function ($entityName) use ($matchDataRepoMock, $settingRepoMock) {
                    if ($entityName === MatchData::class) {
                        return $matchDataRepoMock;
                    }

                    if ($entityName === Setting::class) {
                        return $settingRepoMock;
                    }

                    return true;
                }
            )
        );

        $classementService = new ClassementService(
            $objectManager,
            $this->createMock(EquipeService::class),
            $this->createMock(MatchDataService::class)
        );

        $this->assertEquals(
            $match_data_test,
            $classementService->genereClassementEquipes(
                3,
                'bash',
                5
            )
        );
    }

    /**
     * @test
     */
    public function le_classement_td_est_retournee(): void
    {
        $teamMock0 = $this->createMock(Teams::class);
        $teamMock1 = $this->createMock(Teams::class);
        $teamMock2 = $this->createMock(Teams::class);
        $teamMock3 = $this->createMock(Teams::class);
        $teamMock4 = $this->createMock(Teams::class);

        $match_data_test = [
            'teams' => [$teamMock0, $teamMock1, $teamMock2, $teamMock3, $teamMock4],
            'title' => 'Le plus de TD',
            'class' => 'class_Ttd',
            'type' => 'td',
            'limit' => 5,
            'annee' => 3
        ];

        $matchDataRepoMock = $this->getMockBuilder(MatchData::class)
            ->addMethods(['sousClassementEquipe'])
            ->getMock();

        $matchDataRepoMock->method('sousClassementEquipe')->willReturn(
            [$teamMock0, $teamMock1, $teamMock2, $teamMock3, $teamMock4]
        );

        $settingMock = $this->createMock(Setting::class);
        $settingMock->method('getValue')->willReturn((string) RulesetEnum::BB_2016);

        $settingRepoMock = $this->createMock(ObjectRepository::class);
        $settingRepoMock->method('findOneBy')->willReturn($settingMock);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->will(
            $this->returnCallback(
                function ($entityName) use ($matchDataRepoMock, $settingRepoMock) {
                    if ($entityName === MatchData::class) {
                        return $matchDataRepoMock;
                    }

                    if ($entityName === Setting::class) {
                        return $settingRepoMock;
                    }

                    return true;
                }
            )
        );

        $classementService = new ClassementService(
            $objectManager,
            $this->createMock(EquipeService::class),
            $this->createMock(MatchDataService::class)
        );

        $this->assertEquals(
            $match_data_test,
            $classementService->genereClassementEquipes(
                3,
                'td',
                5
            )
        );
    }

    /**
     * @test
     */
    public function le_classement_dead_est_retournee(): void
    {
        $teamMock0 = $this->createMock(Teams::class);
        $teamMock1 = $this->createMock(Teams::class);
        $teamMock2 = $this->createMock(Teams::class);
        $teamMock3 = $this->createMock(Teams::class);
        $teamMock4 = $this->createMock(Teams::class);

        $match_data_test = [
            'teams' => [$teamMock0, $teamMock1, $teamMock2, $teamMock3, $teamMock4],
            'title' => 'Fournisseurs de cadavres',
            'class' => 'class_Tdead',
            'type' => 'dead',
            'limit' => 5,
            'annee' => 3
        ];

        $playerRepoMock = $this->getMockBuilder(Players::class)
            ->addMethods(['sousClassementEquipeFournisseurDeCadavre'])
            ->getMock();

        $playerRepoMock->method('sousClassementEquipeFournisseurDeCadavre')->willReturn(
            [$teamMock0, $teamMock1, $teamMock2, $teamMock3, $teamMock4]
        );

        $settingMock = $this->createMock(Setting::class);
        $settingMock->method('getValue')->willReturn((string) RulesetEnum::BB_2016);

        $settingRepoMock = $this->createMock(ObjectRepository::class);
        $settingRepoMock->method('findOneBy')->willReturn($settingMock);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->will(
            $this->returnCallback(
                function ($entityName) use ($playerRepoMock, $settingRepoMock) {
                    if ($entityName === Players::class) {
                        return $playerRepoMock;
                    }

                    if ($entityName === Setting::class) {
                        return $settingRepoMock;
                    }

                    return true;
                }
            )
        );

        $classementService = new ClassementService(
            $objectManager,
            $this->createMock(EquipeService::class),
            $this->createMock(MatchDataService::class)
        );

        $this->assertEquals(
            $match_data_test,
            $classementService->genereClassementEquipes(
                3,
                'dead',
                5
            )
        );
    }

    /**
     * @test
     */
    public function le_classement_foul_team_est_retournee(): void
    {
        $teamMock0 = $this->createMock(Teams::class);
        $teamMock1 = $this->createMock(Teams::class);
        $teamMock2 = $this->createMock(Teams::class);
        $teamMock3 = $this->createMock(Teams::class);
        $teamMock4 = $this->createMock(Teams::class);

        $match_data_test = [
            'teams' => [$teamMock0, $teamMock1, $teamMock2, $teamMock3, $teamMock4],
            'title' => 'Les tricheurs',
            'class' => 'class_Tfoul',
            'type' => 'foul',
            'limit' => 5,
            'annee' => 3
        ];

        $matchDataRepoMock = $this->getMockBuilder(MatchData::class)
            ->addMethods(['sousClassementEquipe'])
            ->getMock();

        $matchDataRepoMock->method('sousClassementEquipe')->willReturn(
            [$teamMock0, $teamMock1, $teamMock2, $teamMock3, $teamMock4]
        );

        $settingMock = $this->createMock(Setting::class);
        $settingMock->method('getValue')->willReturn((string) RulesetEnum::BB_2016);

        $settingRepoMock = $this->createMock(ObjectRepository::class);
        $settingRepoMock->method('findOneBy')->willReturn($settingMock);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->will(
            $this->returnCallback(
                function ($entityName) use ($matchDataRepoMock, $settingRepoMock) {
                    if ($entityName === MatchData::class) {
                        return $matchDataRepoMock;
                    }

                    if ($entityName === Setting::class) {
                        return $settingRepoMock;
                    }

                    return true;
                }
            )
        );

        $classementService = new ClassementService(
            $objectManager,
            $this->createMock(EquipeService::class),
            $this->createMock(MatchDataService::class)
        );
        $this->assertEquals(
            $match_data_test,
            $classementService->genereClassementEquipes(
                3,
                'foul',
                5
            )
        );
    }


    /**
     * @test
     */
    public function il_n_y_a_pas_de_donnees(): void
    {

        $match_data_test = [
            'teams' => [],
            'title' => 'Les plus méchants',
            'class' => 'class_Tbash',
            'type' => 'bash',
            'limit' => 5,
            'annee' => 3
        ];

        $matchDataRepoMock = $this->getMockBuilder(MatchData::class)
            ->setMethods(['sousClassementEquipe'])
            ->getMock();

        $matchDataRepoMock->method('sousClassementEquipe')->willReturn(
            []
        );

        $settingMock = $this->createMock(Setting::class);
        $settingMock->method('getValue')->willReturn((string) RulesetEnum::BB_2016);

        $settingRepoMock = $this->createMock(ObjectRepository::class);
        $settingRepoMock->method('findOneBy')->willReturn($settingMock);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->will(
            $this->returnCallback(
                function ($entityName) use ($matchDataRepoMock, $settingRepoMock) {
                    if ($entityName === MatchData::class) {
                        return $matchDataRepoMock;
                    }

                    if ($entityName === Setting::class) {
                        return $settingRepoMock;
                    }

                    return true;
                }
            )
        );

        $classementService = new ClassementService(
            $objectManager,
            $this->createMock(EquipeService::class),
            $this->createMock(MatchDataService::class)
        );

        $this->assertEquals(
            $match_data_test,
            $classementService->genereClassementEquipes(
                3,
                'bash',
                5
            )
        );
    }
}