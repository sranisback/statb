<?php

namespace App\Tests\src\Service\MatchDataService;

use App\Entity\MatchData;
use App\Service\MatchDataService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class LectureLignedUnMatchTest extends KernelTestCase
{
 /**
  * @test
  */
    public function une_ligne_est_lue()
    {
        $matchDataTest = new MatchData();

        $matchDataTest->setBh(1);
        $matchDataTest->setMvp(1);
        $matchDataTest->setTd(1);

        $objectManager = $this->createMock(EntityManagerInterface::class);

        $matchDataService = new matchDataService($objectManager);

        $this->assertEquals('TD: 1, CAS: 1, MVP: 1, ', $matchDataService->lectureLignedUnMatch($matchDataTest));
    }

    /**
     * @test
     */
    public function il_n_y_a_pas_de_donnees()
    {
        $matchDataTest = new MatchData();

        $objectManager = $this->createMock(EntityManagerInterface::class);

        $matchDataService = new matchDataService($objectManager);

        $this->assertEquals('', $matchDataService->lectureLignedUnMatch($matchDataTest));
    }
}