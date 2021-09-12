<?php


namespace App\Tests\src\Service\MatchDataService;


use App\Entity\MatchData;
use App\Service\MatchDataService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class lectureLignedUnMatchTest extends TestCase
{
    /**
     * @test
     */
    public function la_ligne_est_bien_lue()
    {
        $ligneMatch = new MatchData();
        $ligneMatch->setBh(1);
        $ligneMatch->setCp(1);
        $ligneMatch->setMvp(1);

        $matchDateService = new MatchDataService($this->createMock(EntityManagerInterface::class));

        $this->assertEquals('CP: 1, CAS: 1, MVP: 1, ',$matchDateService->lectureLignedUnMatch($ligneMatch));
    }

    /**
     * @test
     */
    public function le_bonus_xp_est_pris_en_compte()
    {
        $ligneMatch = new MatchData();
        $ligneMatch->setBonusSpp(1);

        $matchDateService = new MatchDataService($this->createMock(EntityManagerInterface::class));

        $this->assertEquals('BONUS: 1, ',$matchDateService->lectureLignedUnMatch($ligneMatch));
    }

    /**
     * @test
     */
    public function le_lancer_et_deviation_est_pris_en_compte()
    {
        $ligneMatch = new MatchData();
        $ligneMatch->setDet(1);
        $ligneMatch->setLan(1);

        $matchDateService = new MatchDataService($this->createMock(EntityManagerInterface::class));

        $this->assertEquals('DET: 1, LAN: 1, ',$matchDateService->lectureLignedUnMatch($ligneMatch));
    }

    /**
     * @test
     */
    public function le_carton_rouge_est_pris_en_compte()
    {
        $ligneMatch = new MatchData();
        $ligneMatch->setCartonsRouge(1);

        $matchDateService = new MatchDataService($this->createMock(EntityManagerInterface::class));

        $this->assertEquals('Carton Rouge !, ',$matchDateService->lectureLignedUnMatch($ligneMatch));
    }

    /**
     * @test
     */
    public function le_carton_rouge_est_a_la_fin()
    {
        $ligneMatch = new MatchData();
        $ligneMatch->setBh(1);
        $ligneMatch->setCp(1);
        $ligneMatch->setMvp(1);
        $ligneMatch->setCartonsRouge(1);

        $matchDateService = new MatchDataService($this->createMock(EntityManagerInterface::class));

        $this->assertEquals('CP: 1, CAS: 1, MVP: 1, Carton Rouge !, ',$matchDateService->lectureLignedUnMatch($ligneMatch));
    }
}