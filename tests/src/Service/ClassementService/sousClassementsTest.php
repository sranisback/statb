<?php

namespace App\Tests\src\Service\ClassementService;

use App\Entity\MatchData;
use App\Entity\Players;
use App\Entity\Teams;
use App\Service\ClassementService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class sousClassementsTest extends KernelTestCase
{
    /**
    * @test
    */
    public function le_classement_bashlord_est_retournee()
    {
        $playerMock0 = $this->createMock(Players::class);
        $playerMock1 = $this->createMock(Players::class);
        $playerMock2 = $this->createMock(Players::class);
        $playerMock3 = $this->createMock(Players::class);
        $playerMock4 = $this->createMock(Players::class);

        $match_data_test = [
            'players' => [$playerMock0, $playerMock1, $playerMock2, $playerMock3, $playerMock4],
            'title' => 'Le Bash Lord - Record CAS',
            'class' => 'class_bash',
            'type' => 'bash',
            'teamorplayer' => 'player',
            'limit' => 5,
        ];

        $matchDataRepoMock = $this->getMockBuilder(MatchData::class)
            ->setMethods(['SClassement'])
            ->getMock();

        $matchDataRepoMock->method('SClassement')->willReturn(
            [$playerMock0, $playerMock1, $playerMock2, $playerMock3, $playerMock4]
        );

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchDataRepoMock);

        $classementService = new ClassementService($objectManager);

        $this->assertEquals(
            $match_data_test,
            $classementService->sousClassements(
                3,
                'bash',
                'player',
                5
            )
        );
    }

    /**
     * @test
     */
    public function le_classement_marqueur_est_retournee()
    {
        $playerMock0 = $this->createMock(Players::class);
        $playerMock1 = $this->createMock(Players::class);
        $playerMock2 = $this->createMock(Players::class);
        $playerMock3 = $this->createMock(Players::class);
        $playerMock4 = $this->createMock(Players::class);

        $match_data_test = [
            'players' => [$playerMock0, $playerMock1, $playerMock2, $playerMock3, $playerMock4],
            'title' => 'Le Marqueur - Record TD',
            'class' => 'class_td',
            'type' => 'td',
            'teamorplayer' => 'player',
            'limit' => 5,
        ];

        $matchDataRepoMock = $this->getMockBuilder(MatchData::class)
            ->setMethods(['SClassement'])
            ->getMock();

        $matchDataRepoMock->method('SClassement')->willReturn(
            [$playerMock0, $playerMock1, $playerMock2, $playerMock3, $playerMock4]
        );

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchDataRepoMock);

        $classementService = new ClassementService($objectManager);

        $this->assertEquals(
            $match_data_test,
            $classementService->sousClassements(
                3,
                'td',
                'player',
                5
            )
        );
    }

    /**
     * @test
     */
    public function le_classement_spp_est_retournee()
    {
        $playerMock0 = $this->createMock(Players::class);
        $playerMock1 = $this->createMock(Players::class);
        $playerMock2 = $this->createMock(Players::class);
        $playerMock3 = $this->createMock(Players::class);
        $playerMock4 = $this->createMock(Players::class);

        $match_data_test = [
            'players' => [$playerMock0, $playerMock1, $playerMock2, $playerMock3, $playerMock4],
            'title' => 'Le Meilleur - Record SPP',
            'class' => 'class_xp',
            'type' => 'xp',
            'teamorplayer' => 'player',
            'limit' => 5,
        ];

        $matchDataRepoMock = $this->getMockBuilder(MatchData::class)
            ->setMethods(['SClassement'])
            ->getMock();

        $matchDataRepoMock->method('SClassement')->willReturn(
            [$playerMock0, $playerMock1, $playerMock2, $playerMock3, $playerMock4]
        );

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchDataRepoMock);

        $classementService = new ClassementService($objectManager);

        $this->assertEquals(
            $match_data_test,
            $classementService->sousClassements(
                3,
                'xp',
                'player',
                5
            )
        );
    }

    /**
     * @test
     */
    public function le_classement_passes_est_retournee()
    {
        $playerMock0 = $this->createMock(Players::class);
        $playerMock1 = $this->createMock(Players::class);
        $playerMock2 = $this->createMock(Players::class);
        $playerMock3 = $this->createMock(Players::class);
        $playerMock4 = $this->createMock(Players::class);

        $match_data_test = [
            'players' => [$playerMock0, $playerMock1, $playerMock2, $playerMock3, $playerMock4],
            'title' => 'La Main d\'or - Record Passes',
            'class' => 'class_pass',
            'type' => 'pass',
            'teamorplayer' => 'player',
            'limit' => 5,
        ];

        $matchDataRepoMock = $this->getMockBuilder(MatchData::class)
            ->setMethods(['SClassement'])
            ->getMock();

        $matchDataRepoMock->method('SClassement')->willReturn(
            [$playerMock0, $playerMock1, $playerMock2, $playerMock3, $playerMock4]
        );

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchDataRepoMock);

        $classementService = new ClassementService($objectManager);

        $this->assertEquals(
            $match_data_test,
            $classementService->sousClassements(
                3,
                'pass',
                'player',
                5
            )
        );
    }

    /**
     * @test
     */
    public function le_classement_foul_est_retournee()
    {
        $playerMock0 = $this->createMock(Players::class);
        $playerMock1 = $this->createMock(Players::class);
        $playerMock2 = $this->createMock(Players::class);
        $playerMock3 = $this->createMock(Players::class);
        $playerMock4 = $this->createMock(Players::class);

        $match_data_test = [
            'players' => [$playerMock0, $playerMock1, $playerMock2, $playerMock3, $playerMock4],
            'title' => 'Le Tricheur - Record Fautes',
            'class' => 'class_foul',
            'type' => 'foul',
            'teamorplayer' => 'player',
            'limit' => 5,
        ];

        $matchDataRepoMock = $this->getMockBuilder(MatchData::class)
            ->setMethods(['SClassement'])
            ->getMock();

        $matchDataRepoMock->method('SClassement')->willReturn(
            [$playerMock0, $playerMock1, $playerMock2, $playerMock3, $playerMock4]
        );

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchDataRepoMock);

        $classementService = new ClassementService($objectManager);

        $this->assertEquals(
            $match_data_test,
            $classementService->sousClassements(
                3,
                'foul',
                'player',
                5
            )
        );
    }

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
            'teamorplayer' => 'team',
            'limit' => 5,
        ];

        $matchDataRepoMock = $this->getMockBuilder(MatchData::class)
            ->setMethods(['SClassement'])
            ->getMock();

        $matchDataRepoMock->method('SClassement')->willReturn(
            [$teamMock0, $teamMock1, $teamMock2, $teamMock3, $teamMock4]
        );

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchDataRepoMock);

        $classementService = new ClassementService($objectManager);

        $this->assertEquals(
            $match_data_test,
            $classementService->sousClassements(
                3,
                'bash',
                'team',
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
            'teamorplayer' => 'team',
            'limit' => 5,
        ];

        $matchDataRepoMock = $this->getMockBuilder(MatchData::class)
            ->setMethods(['SClassement'])
            ->getMock();

        $matchDataRepoMock->method('SClassement')->willReturn(
            [$teamMock0, $teamMock1, $teamMock2, $teamMock3, $teamMock4]
        );

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchDataRepoMock);

        $classementService = new ClassementService($objectManager);

        $this->assertEquals(
            $match_data_test,
            $classementService->sousClassements(
                3,
                'td',
                'team',
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
            'teamorplayer' => 'team',
            'limit' => 5,
        ];

        $matchDataRepoMock = $this->getMockBuilder(MatchData::class)
            ->setMethods(['SClassement'])
            ->getMock();

        $matchDataRepoMock->method('SClassement')->willReturn(
            [$teamMock0, $teamMock1, $teamMock2, $teamMock3, $teamMock4]
        );

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchDataRepoMock);

        $classementService = new ClassementService($objectManager);

        $this->assertEquals(
            $match_data_test,
            $classementService->sousClassements(
                3,
                'dead',
                'team',
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
            'teamorplayer' => 'team',
            'limit' => 5,
        ];

        $matchDataRepoMock = $this->getMockBuilder(MatchData::class)
            ->setMethods(['SClassement'])
            ->getMock();

        $matchDataRepoMock->method('SClassement')->willReturn(
            [$teamMock0, $teamMock1, $teamMock2, $teamMock3, $teamMock4]
        );

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchDataRepoMock);

        $classementService = new ClassementService($objectManager);

        $this->assertEquals(
            $match_data_test,
            $classementService->sousClassements(
                3,
                'foul',
                'team',
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
            'players' => [],
            'title' => 'Le Bash Lord - Record CAS',
            'class' => 'class_bash',
            'type' => 'bash',
            'teamorplayer' => 'player',
            'limit' => 5,
        ];

        $matchDataRepoMock = $this->getMockBuilder(MatchData::class)
            ->setMethods(['SClassement'])
            ->getMock();

        $matchDataRepoMock->method('SClassement')->willReturn(
            []
        );

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchDataRepoMock);

        $classementService = new ClassementService($objectManager);

        $this->assertEquals(
            $match_data_test,
            $classementService->sousClassements(
                3,
                'bash',
                'player',
                5
            )
        );
    }
}