<?php

namespace App\Tests\src\Service\ClassementService;

use App\Entity\MatchData;
use App\Entity\Teams;
use App\Service\ClassementService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class sousClassementsEquipeTest extends KernelTestCase
{
    /**
     * @test
     */
    public function le_classement_bash_est_retournee()
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
        ];

        $matchDataRepoMock = $this->getMockBuilder(MatchData::class)
            ->setMethods(['sousClassementEquipe'])
            ->getMock();

        $matchDataRepoMock->method('sousClassementEquipe')->willReturn(
            [$teamMock0, $teamMock1, $teamMock2, $teamMock3, $teamMock4]
        );

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchDataRepoMock);

        $classementService = new ClassementService($objectManager);

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
    public function le_classement_td_est_retournee()
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
        ];

        $matchDataRepoMock = $this->getMockBuilder(MatchData::class)
            ->setMethods(['sousClassementEquipe'])
            ->getMock();

        $matchDataRepoMock->method('sousClassementEquipe')->willReturn(
            [$teamMock0, $teamMock1, $teamMock2, $teamMock3, $teamMock4]
        );

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchDataRepoMock);

        $classementService = new ClassementService($objectManager);

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
    public function le_classement_dead_est_retournee()
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
        ];

        $matchDataRepoMock = $this->getMockBuilder(MatchData::class)
            ->setMethods(['sousClassementEquipeFournisseurDeCadavre'])
            ->getMock();

        $matchDataRepoMock->method('sousClassementEquipeFournisseurDeCadavre')->willReturn(
            [$teamMock0, $teamMock1, $teamMock2, $teamMock3, $teamMock4]
        );

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchDataRepoMock);

        $classementService = new ClassementService($objectManager);

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
    public function le_classement_foul_team_est_retournee()
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
        ];

        $matchDataRepoMock = $this->getMockBuilder(MatchData::class)
            ->setMethods(['sousClassementEquipe'])
            ->getMock();

        $matchDataRepoMock->method('sousClassementEquipe')->willReturn(
            [$teamMock0, $teamMock1, $teamMock2, $teamMock3, $teamMock4]
        );

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchDataRepoMock);

        $classementService = new ClassementService($objectManager);

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
    public function il_n_y_a_pas_de_donnees()
    {

        $match_data_test = [
            'teams' => [],
            'title' => 'Les plus méchants',
            'class' => 'class_Tbash',
            'type' => 'bash',
            'limit' => 5,
        ];

        $matchDataRepoMock = $this->getMockBuilder(MatchData::class)
            ->setMethods(['sousClassementEquipe'])
            ->getMock();

        $matchDataRepoMock->method('sousClassementEquipe')->willReturn(
            []
        );

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchDataRepoMock);

        $classementService = new ClassementService($objectManager);

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