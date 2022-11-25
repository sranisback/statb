<?php


namespace App\Tests\src\Service\EquipeService;


use App\Entity\Matches;
use App\Entity\Teams;
use App\Service\EquipeService;
use App\Tests\src\TestServiceFactory\EquipeServiceTestFactory;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class CalculBonusPoulpiTest extends TestCase
{
    private EquipeService $EquipeService;

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

        $this->EquipeService = (new EquipeServiceTestFactory)->getInstance(
            $objectManager
        );
    }

    /**
     * @test
     */
    public function le_calcul_du_bonus_est_correct_victoire_pts_plus_bas()
    {
        $this->matches->setTeam1Score(1);
        $this->matches->setTeam2Score(0);
        $this->matches->setScoreClassementTeam1(50);
        $this->matches->setScoreClassementTeam2(200);

        $this->assertEquals(15, $this->EquipeService->calculBonusPoulpi($this->equipe1));
    }


    /**
     * @test
     */
    public function le_calcul_du_bonus_est_correct_pts_plus_haut()
    {
        $this->matches->setTeam1Score(1);
        $this->matches->setTeam2Score(0);
        $this->matches->setScoreClassementTeam1(100);
        $this->matches->setScoreClassementTeam2(50);

        $this->assertEquals(-5, $this->EquipeService->calculBonusPoulpi($this->equipe1));
    }

    /**
     * @test
     */
    public function le_calcul_du_bonus_est_correct_defaite_pts_plus_bas()
    {
        $this->matches->setTeam1Score(0);
        $this->matches->setTeam2Score(1);
        $this->matches->setScoreClassementTeam1(50);
        $this->matches->setScoreClassementTeam2(100);

        $this->assertEquals(5, $this->EquipeService->calculBonusPoulpi($this->equipe1));
    }

    /**
     * @test
     */
    public function le_calcul_du_bonus_est_correct_defaite_pts_plus_haut()
    {
        $this->matches->setTeam1Score(0);
        $this->matches->setTeam2Score(1);
        $this->matches->setScoreClassementTeam1(100);
        $this->matches->setScoreClassementTeam2(50);

        $this->assertEquals(-5, $this->EquipeService->calculBonusPoulpi($this->equipe1));
    }

    /**
     * @test
     */
    public function le_calcul_du_bonus_est_correct_egalite_pts_plus_bas()
    {
        $this->matches->setTeam1Score(0);
        $this->matches->setTeam2Score(0);
        $this->matches->setScoreClassementTeam1(50);
        $this->matches->setScoreClassementTeam2(100);

        $this->assertEquals(5, $this->EquipeService->calculBonusPoulpi($this->equipe1));
    }

    /**
     * @test
     */
    public function le_calcul_du_bonus_est_correct_egalite_pts_plus_haut()
    {
        $this->matches->setTeam1Score(0);
        $this->matches->setTeam2Score(0);
        $this->matches->setScoreClassementTeam1(100);
        $this->matches->setScoreClassementTeam2(50);

        $this->assertEquals(-5, $this->EquipeService->calculBonusPoulpi($this->equipe1));
    }
}