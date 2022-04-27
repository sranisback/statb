<?php

namespace App\Tests\src\Service\EquipeGestionService;


use App\Entity\Races;
use App\Entity\RacesBb2020;
use App\Entity\Teams;
use App\Enum\RulesetEnum;
use App\Service\EquipeGestionService;
use App\Service\InducementService;
use App\Service\InfosService;
use App\Service\PlayerService;
use App\Service\SettingsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class tvDelEquipeTest extends KernelTestCase
{
    /**
     * @test
     */
    public function la_tv_est_calculee_correctement_bb2016(): void
    {
        $raceTest = new Races();
        $raceTest->setCostRr(50_000);

        $equipeTest = new Teams;
        $equipeTest->setFf(1);
        $equipeTest->setFRace($raceTest);
        $equipeTest->setRuleset(RulesetEnum::BB_2016);

        $objectManager = $this->createMock(EntityManagerInterface::class);

        $playerServiceMock = $this->createMock(PlayerService::class);
        $playerServiceMock->method('coutTotalJoueurs')->willReturn(100_000);

        $inducementServiceMock = $this->createMock(InducementService::class);
        $inducementServiceMock->method('valeurInducementDelEquipe')->willReturn(
            [
                'rerolls' => 200_000,
                'pop' => 10_000,
                'asscoaches' => 0,
                'cheerleader' => 0,
                'apo' => 0,
                'total' => 210_000
            ]
        );

        $equipeGestionService = new EquipeGestionService(
            $objectManager,
            $this->createMock(SettingsService::class),
            $this->createMock(InfosService::class),
            $inducementServiceMock
        );

        $this->assertEquals(310_000, $equipeGestionService->tvDelEquipe($equipeTest, $playerServiceMock));
    }

    /**
     * @test
     */
    public function la_tv_est_calculee_correctement_bb2020(): void
    {
        $raceTest = new RacesBb2020();
        $raceTest->setCostRr(50_000);

        $equipeTest = new Teams;
        $equipeTest->setRace($raceTest);
        $equipeTest->setRuleset(RulesetEnum::BB_2020);

        $objectManager = $this->createMock(EntityManagerInterface::class);

        $playerServiceMock = $this->createMock(PlayerService::class);
        $playerServiceMock->method('coutTotalJoueurs')->willReturn(100_000);

        $inducementServiceMock = $this->createMock(InducementService::class);
        $inducementServiceMock->method('valeurInducementDelEquipe')->willReturn(
            [
                'rerolls' => 200_000,
                'pop' => 0,
                'asscoaches' => 0,
                'cheerleader' => 0,
                'apo' => 0,
                'total' => 200_000
            ]
        );

        $equipeGestionService = new EquipeGestionService(
            $objectManager,
            $this->createMock(SettingsService::class),
            $this->createMock(InfosService::class),
            $inducementServiceMock
        );

        $this->assertEquals(300_000, $equipeGestionService->tvDelEquipe($equipeTest, $playerServiceMock));
    }
}

