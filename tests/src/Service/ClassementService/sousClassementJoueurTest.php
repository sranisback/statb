<?php

namespace App\Tests\src\Service\ClassementService;

use App\Entity\MatchData;
use App\Entity\Players;
use App\Service\ClassementService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class sousClassementJoueurTest extends KernelTestCase
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
            'limit' => 5,
            'annee' => 3
        ];

        $matchDataRepoMock = $this->getMockBuilder(MatchData::class)
            ->setMethods(['sousClassementJoueur'])
            ->getMock();

        $matchDataRepoMock->method('sousClassementJoueur')->willReturn(
            [$playerMock0, $playerMock1, $playerMock2, $playerMock3, $playerMock4]
        );

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchDataRepoMock);

        $classementService = new ClassementService($objectManager);

        $this->assertEquals(
            $match_data_test,
            $classementService->genereClassementJoueurs(
                3,
                'bash',
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
             'limit' => 5,
            'annee' => 3
        ];

        $matchDataRepoMock = $this->getMockBuilder(MatchData::class)
            ->setMethods(['sousClassementJoueur'])
            ->getMock();

        $matchDataRepoMock->method('sousClassementJoueur')->willReturn(
            [$playerMock0, $playerMock1, $playerMock2, $playerMock3, $playerMock4]
        );

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchDataRepoMock);

        $classementService = new ClassementService($objectManager);

        $this->assertEquals(
            $match_data_test,
            $classementService->genereClassementJoueurs(
                3,
                'td',
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
            'limit' => 5,
            'annee' => 3
        ];

        $matchDataRepoMock = $this->getMockBuilder(MatchData::class)
            ->setMethods(['sousClassementJoueur'])
            ->getMock();

        $matchDataRepoMock->method('sousClassementJoueur')->willReturn(
            [$playerMock0, $playerMock1, $playerMock2, $playerMock3, $playerMock4]
        );

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchDataRepoMock);

        $classementService = new ClassementService($objectManager);

        $this->assertEquals(
            $match_data_test,
            $classementService->genereClassementJoueurs(
                3,
                'xp',
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
            'limit' => 5,
            'annee' => 3
        ];

        $matchDataRepoMock = $this->getMockBuilder(MatchData::class)
            ->setMethods(['sousClassementJoueur'])
            ->getMock();

        $matchDataRepoMock->method('sousClassementJoueur')->willReturn(
            [$playerMock0, $playerMock1, $playerMock2, $playerMock3, $playerMock4]
        );

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchDataRepoMock);

        $classementService = new ClassementService($objectManager);

        $this->assertEquals(
            $match_data_test,
            $classementService->genereClassementJoueurs(
                3,
                'pass',
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
            'limit' => 5,
            'annee' => 3
        ];

        $matchDataRepoMock = $this->getMockBuilder(MatchData::class)
            ->setMethods(['sousClassementJoueur'])
            ->getMock();

        $matchDataRepoMock->method('sousClassementJoueur')->willReturn(
            [$playerMock0, $playerMock1, $playerMock2, $playerMock3, $playerMock4]
        );

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchDataRepoMock);

        $classementService = new ClassementService($objectManager);

        $this->assertEquals(
            $match_data_test,
            $classementService->genereClassementJoueurs(
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
            'players' => [],
            'title' => 'Le Bash Lord - Record CAS',
            'class' => 'class_bash',
            'type' => 'bash',
            'limit' => 5,
            'annee' => 3
        ];

        $matchDataRepoMock = $this->getMockBuilder(MatchData::class)
            ->setMethods(['sousClassementJoueur'])
            ->getMock();

        $matchDataRepoMock->method('sousClassementJoueur')->willReturn(
            []
        );

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchDataRepoMock);

        $classementService = new ClassementService($objectManager);

        $this->assertEquals(
            $match_data_test,
            $classementService->genereClassementJoueurs(
                3,
                'bash',
                5
            )
        );
    }
}