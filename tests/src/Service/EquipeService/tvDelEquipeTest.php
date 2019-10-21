<?php

namespace App\Tests\src\Service\EquipeService;


use App\Entity\GameDataPlayers;
use App\Entity\Players;
use App\Entity\Races;
use App\Entity\Teams;
use App\Service\EquipeService;
use App\Service\PlayerService;
use App\Service\SettingsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class tvDelEquipeTest extends KernelTestCase
{
    /**
     * @test
     */
    public function la_tv_est_calculee_correctement()
    {
        $raceTest = new Races();
        $raceTest->setCostRr(50000);

        $equipeTest = new Teams;
        $equipeTest->setRerolls(4);
        $equipeTest->setFRace($raceTest);

        $objectManager = $this->createMock(EntityManagerInterface::class);

        $playerServiceMock = $this->createMock(PlayerService::class);
        $playerServiceMock->method('coutTotalJoueurs')->willReturn(100000);

        $equipeService = new EquipeService(
            $objectManager,
            $this->createMock(SettingsService::class)
        );

        $this->assertEquals(300000, $equipeService->tvDelEquipe($equipeTest, $playerServiceMock));
    }

}

