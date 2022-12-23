<?php


namespace App\Tests\src\Service\EquipeService;


use App\Entity\Matches;
use App\Entity\Teams;
use App\Service\EquipeGestionService;
use App\Service\EquipeService;
use App\Service\InducementService;
use App\Service\SettingsService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class MaxScoreTest extends TestCase
{
    /**
     * @test
     */
    public function le_score_est_maxe_team1_plus_grand_score_win()
    {
        $resultat = ['win' => 1, 'draw' => 0, 'loss' => 0 ];

        $equipe = new Teams();

        $match = new Matches();
        $match->setScoreClassementTeam1(150);
        $match->setScoreClassementTeam2(50);
        $match->setTeam1($equipe);

        $equipeService = new EquipeService(
            $this->createMock(EntityManagerInterface::class),
            $this->createMock(SettingsService::class),
            $this->createMock(InducementService::class),
            $this->createMock(EquipeGestionService::class)
        );

        $this->assertEquals(5, $equipeService->maxScore(25, $resultat, $match, $equipe));
    }

    /**
     * @test
     */
    public function le_score_est_maxe_team1_plus_petit_score_win()
    {
        $resultat = ['win' => 1, 'draw' => 0, 'loss' => 0 ];

        $equipe = new Teams();

        $match = new Matches();
        $match->setScoreClassementTeam1(50);
        $match->setScoreClassementTeam2(150);
        $match->setTeam1($equipe);

        $equipeService = new EquipeService(
            $this->createMock(EntityManagerInterface::class),
            $this->createMock(SettingsService::class),
            $this->createMock(InducementService::class),
            $this->createMock(EquipeGestionService::class)
        );

        $this->assertEquals(15, $equipeService->maxScore(25, $resultat, $match, $equipe));
    }

    /**
     * @test
     */
    public function le_score_est_maxe_team1_score_egale_win()
    {
        $resultat = ['win' => 1, 'draw' => 0, 'loss' => 0 ];

        $equipe = new Teams();

        $match = new Matches();
        $match->setScoreClassementTeam1(100);
        $match->setScoreClassementTeam2(100);
        $match->setTeam1($equipe);

        $equipeService = new EquipeService(
            $this->createMock(EntityManagerInterface::class),
            $this->createMock(SettingsService::class),
            $this->createMock(InducementService::class),
            $this->createMock(EquipeGestionService::class)
        );

        $this->assertEquals(25, $equipeService->maxScore(25, $resultat, $match, $equipe));
    }

    /**
     * @test
     */
    public function le_score_est_maxe_team1_plus_petit_score_loss()
    {
        $resultat = ['win' => 0, 'draw' => 0, 'loss' => 1 ];

        $equipe = new Teams();

        $match = new Matches();
        $match->setScoreClassementTeam1(50);
        $match->setScoreClassementTeam2(150);
        $match->setTeam1($equipe);

        $equipeService = new EquipeService(
            $this->createMock(EntityManagerInterface::class),
            $this->createMock(SettingsService::class),
            $this->createMock(InducementService::class),
            $this->createMock(EquipeGestionService::class)
        );

        $this->assertEquals(-5, $equipeService->maxScore(-25, $resultat, $match, $equipe));
    }

    /**
     * @test
     */
    public function le_score_est_maxe_team1_plus_grand_score_loss()
    {
        $resultat = ['win' => 0, 'draw' => 0, 'loss' => 1 ];

        $equipe = new Teams();

        $match = new Matches();
        $match->setScoreClassementTeam1(150);
        $match->setScoreClassementTeam2(50);
        $match->setTeam1($equipe);

        $equipeService = new EquipeService(
            $this->createMock(EntityManagerInterface::class),
            $this->createMock(SettingsService::class),
            $this->createMock(InducementService::class),
            $this->createMock(EquipeGestionService::class)
        );

        $this->assertEquals(-15, $equipeService->maxScore(-25, $resultat, $match, $equipe));
    }

    /**
     * @test
     */
    public function le_score_est_maxe_team1_score_egale_loss()
    {
        $resultat = ['win' => 0, 'draw' => 0, 'loss' => 1 ];

        $equipe = new Teams();

        $match = new Matches();
        $match->setScoreClassementTeam1(100);
        $match->setScoreClassementTeam2(100);
        $match->setTeam1($equipe);

        $equipeService = new EquipeService(
            $this->createMock(EntityManagerInterface::class),
            $this->createMock(SettingsService::class),
            $this->createMock(InducementService::class),
            $this->createMock(EquipeGestionService::class)
        );

        $this->assertEquals(-25, $equipeService->maxScore(-25, $resultat, $match, $equipe));
    }

    /**
     * @test
     */
    public function le_score_est_maxe_team1_plus_petit_score_draw()
    {
        $resultat = ['win' => 0, 'draw' => 1, 'loss' => 0 ];

        $equipe = new Teams();

        $match = new Matches();
        $match->setScoreClassementTeam1(50);
        $match->setScoreClassementTeam2(150);
        $match->setTeam1($equipe);

        $equipeService = new EquipeService(
            $this->createMock(EntityManagerInterface::class),
            $this->createMock(SettingsService::class),
            $this->createMock(InducementService::class),
            $this->createMock(EquipeGestionService::class)
        );

        $this->assertEquals(-5, $equipeService->maxScore(-25, $resultat, $match, $equipe));
    }

    /**
     * @test
     */
    public function le_score_est_maxe_team1_plus_grand_score_draw()
    {
        $resultat = ['win' => 0, 'draw' => 1, 'loss' => 0 ];

        $equipe = new Teams();

        $match = new Matches();
        $match->setScoreClassementTeam1(150);
        $match->setScoreClassementTeam2(50);
        $match->setTeam1($equipe);

        $equipeService = new EquipeService(
            $this->createMock(EntityManagerInterface::class),
            $this->createMock(SettingsService::class),
            $this->createMock(InducementService::class),
            $this->createMock(EquipeGestionService::class)
        );

        $this->assertEquals(5, $equipeService->maxScore(25, $resultat, $match, $equipe));
    }

    /**
     * @test
     */
    public function le_score_est_maxe_team1_score_egale_draw()
    {
        $resultat = ['win' => 0, 'draw' => 1, 'loss' => 0 ];

        $equipe = new Teams();

        $match = new Matches();
        $match->setScoreClassementTeam1(100);
        $match->setScoreClassementTeam2(100);
        $match->setTeam1($equipe);

        $equipeService = new EquipeService(
            $this->createMock(EntityManagerInterface::class),
            $this->createMock(SettingsService::class),
            $this->createMock(InducementService::class),
            $this->createMock(EquipeGestionService::class)
        );

        $this->assertEquals(0, $equipeService->maxScore(0, $resultat, $match, $equipe));
    }
}