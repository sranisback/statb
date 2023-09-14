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
    public function le_score_est_bien_calcule_equipe_plus_petit_score()
    {
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

        $this->assertEquals(5, $equipeService->maxScore(5, $match, $equipe));
    }

    /**
     * @test
     */
    public function le_score_est_bien_calcule_equipe_plus_grand_score()
    {
        $equipe = new Teams();

        $match = new Matches();
        $match->setTeam1($equipe);
        $match->setScoreClassementTeam1(150);
        $match->setScoreClassementTeam2(50);

        $equipeService = new EquipeService(
            $this->createMock(EntityManagerInterface::class),
            $this->createMock(SettingsService::class),
            $this->createMock(InducementService::class),
            $this->createMock(EquipeGestionService::class)
        );

        $this->assertEquals(-5, $equipeService->maxScore(5, $match, $equipe));
    }
}