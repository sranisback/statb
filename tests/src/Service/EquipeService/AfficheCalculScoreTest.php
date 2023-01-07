<?php


namespace App\Tests\src\Service\EquipeService;


use App\Entity\Matches;
use App\Entity\ScoreCalcul;
use App\Entity\Teams;
use App\Service\EquipeGestionService;
use App\Service\EquipeService;
use App\Service\InducementService;
use App\Service\SettingsService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class AfficheCalculScoreTest extends TestCase
{
    /**
     * @test
     */
    public function un_tableau_est_renvoye()
    {
        $equipe = new Teams();

        $equipe2 = new Teams();
        $equipe2->setName('adv');

        $match = new Matches();
        $match->setTeam1($equipe);
        $match->setTeam2($equipe2);
        $match->setScoreClassementTeam1(100);
        $match->setScoreClassementTeam2(50);
        $match->setTeam1Score(1);
        $match->setTeam2Score(0);

        $scoreCalcul = new ScoreCalcul();
        $scoreCalcul->setTeams($equipe);
        $scoreCalcul->setMatchCible($match);
        $scoreCalcul->setBonus(6);
        $scoreCalcul->setLostPoint(1);

        $scoreCalculRepo = $this->getMockBuilder(ScoreCalcul::class)
            ->addMethods(['findBy'])
            ->getMock();

        $scoreCalculRepo->method('findBy')->willReturn([$scoreCalcul]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($scoreCalculRepo);

        $settingService = $this->createMock(SettingsService::class);
        $settingService->method('pointsEnCours')->willReturn([10, 0, -10]);

        $equipeService = new EquipeService(
            $objectManager,
            $settingService,
            $this->createMock(InducementService::class),
            $this->createMock(EquipeGestionService::class)
        );

        $result = [[
            'scoreDebutMatch' => $match->getScoreClassementTeam1(),
            'scoreAdv' => $match->getScoreClassementTeam2(),
            'mouvement' => 10,
            'bonus' => 6.0,
            'ajustement' => 1.0,
            'resultat' =>  'victoire',
            'mouvementTotal' => 16.0,
            'match' => $match,
            'equipeAdv' => $equipe2
        ]];

        $this->assertEquals($result, $equipeService->afficheCalculScore($equipe));
    }
}