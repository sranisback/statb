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

class CalculBonusPoulpiTest extends TestCase
{
    private EquipeService $equipeService;

    private Teams $equipe1;

    private Matches $matches;

    public function setUp(): void
    {
        parent::setUp();

        $this->equipe1 = new Teams();

        $equipe2 = new Teams();

        $this->matches = new Matches();
        $this->matches->setTeam1($this->equipe1);
        $this->matches->setTeam2($equipe2);

        $matchRepoMock = $this->getMockBuilder(Matches::class)
            ->addMethods(['listeDesMatchs'])
            ->getMock();

        $matchRepoMock->method('listeDesMatchs')->willReturn([$this->matches]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchRepoMock);

        $this->equipeService = new EquipeService(
            $objectManager,
            $this->createMock(SettingsService::class),
            $this->createMock(InducementService::class),
            $this->createMock(EquipeGestionService::class)
        );

    }


    /**
     * @test
     */
    public function le_calcul_du_bonus_est_correct_pts_plus_haut()
    {
        $this->matches->setScoreClassementTeam1(100);
        $this->matches->setScoreClassementTeam2(50);

        $this->assertEqualsWithDelta(-5, $this->equipeService->calculBonusPoulpi($this->equipe1), 0);
    }

    /**
     * @test
     */
    public function le_calcul_du_bonus_est_correct_pts_plus_bas()
    {
        $this->matches->setScoreClassementTeam1(50);
        $this->matches->setScoreClassementTeam2(100);

        $this->assertEqualsWithDelta(5, $this->equipeService->calculBonusPoulpi($this->equipe1), 0);
    }

    /**
     * @test
     */
    public function le_calcul_du_bonus_est_decimal()
    {
        $this->matches->setScoreClassementTeam1(53);
        $this->matches->setScoreClassementTeam2(100);

        $this->assertEqualsWithDelta(4.7, $this->equipeService->calculBonusPoulpi($this->equipe1), 0);
    }
}