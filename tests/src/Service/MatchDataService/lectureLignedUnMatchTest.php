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

}