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
    public function le_score_est_maxe_win()
    {
        $resultat = ['win' => 1, 'draw' => 0, 'loss' => 0 ];

        $equipe = new Teams();

        $match = new Matches();
        $match->setTeam1($equipe);
        $match->setScoreClassementTeam1(50);
        $match->setScoreClassementTeam2(150);

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
    public function le_score_est_min_win()
    {
        $resultat = ['win' => 1, 'draw' => 0, 'loss' => 0 ];

        $equipe = new Teams();

        $match = new Matches();
        $match->setTeam1($equipe);
        $match->setScoreClassementTeam1(50);
        $match->setScoreClassementTeam2(150);

        $equipeService = new EquipeService(
            $this->createMock(EntityManagerInterface::class),
            $this->createMock(SettingsService::class),
            $this->createMock(InducementService::class),
            $this->createMock(EquipeGestionService::class)
        );

        $this->assertEquals(5, $equipeService->maxScore(2, $resultat, $match, $equipe));
    }

    /**
     * @test
     */
    public function le_score_est_max_loss()
    {
        $resultat = ['win' => 0, 'draw' => 0, 'loss' => 1 ];

        $equipe = new Teams();

        $match = new Matches();
        $match->setTeam1($equipe);
        $match->setScoreClassementTeam1(50);
        $match->setScoreClassementTeam2(150);

        $equipeService = new EquipeService(
            $this->createMock(EntityManagerInterface::class),
            $this->createMock(SettingsService::class),
            $this->createMock(InducementService::class),
            $this->createMock(EquipeGestionService::class)
        );

        $this->assertEquals(-5, $equipeService->maxScore(2, $resultat, $match, $equipe));
    }

    /**
     * @test
     */
    public function le_score_est_min_loss()
    {
        $resultat = ['win' => 0, 'draw' => 0, 'loss' => 1 ];

        $equipe = new Teams();

        $match = new Matches();
        $match->setTeam1($equipe);
        $match->setScoreClassementTeam1(50);
        $match->setScoreClassementTeam2(150);

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
    public function le_score_est_min_draw()
    {
        $resultat = ['win' => 0, 'draw' => 1, 'loss' => 0 ];

        $equipe = new Teams();

        $match = new Matches();
        $match->setTeam1($equipe);
        $match->setScoreClassementTeam1(50);
        $match->setScoreClassementTeam2(150);

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
    public function le_score_est_max_draw()
    {
        $resultat = ['win' => 0, 'draw' => 1, 'loss' => 0 ];

        $equipe = new Teams();

        $match = new Matches();
        $match->setTeam1($equipe);
        $match->setScoreClassementTeam1(50);
        $match->setScoreClassementTeam2(150);

        $equipeService = new EquipeService(
            $this->createMock(EntityManagerInterface::class),
            $this->createMock(SettingsService::class),
            $this->createMock(InducementService::class),
            $this->createMock(EquipeGestionService::class)
        );

        $this->assertEquals(5, $equipeService->maxScore(25, $resultat, $match, $equipe));
    }
}