<?php

namespace App\Tests\src\Service\EquipeService;


use App\Entity\Races;
use App\Entity\RacesBb2020;
use App\Entity\Teams;
use App\Enum\RulesetEnum;
use App\Service\EquipeService;
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
        $equipeTest->setRerolls(4);
        $equipeTest->setFf(1);
        $equipeTest->setFRace($raceTest);
        $equipeTest->setRuleset(RulesetEnum::BB_2016);

        $objectManager = $this->createMock(EntityManagerInterface::class);

        $playerServiceMock = $this->createMock(PlayerService::class);
        $playerServiceMock->method('coutTotalJoueurs')->willReturn(100_000);

        $equipeService = new EquipeService(
            $objectManager,
            $this->createMock(SettingsService::class),
            $this->createMock(InfosService::class)
        );

        $this->assertEquals(310_000, $equipeService->tvDelEquipe($equipeTest, $playerServiceMock));
    }

    /**
     * @test
     */
    public function la_tv_est_calculee_correctement_bb2020(): void
    {
        $raceTest = new RacesBb2020();
        $raceTest->setCostRr(50_000);

        $equipeTest = new Teams;
        $equipeTest->setRerolls(4);
        $equipeTest->setFf(1);
        $equipeTest->setRace($raceTest);
        $equipeTest->setRuleset(RulesetEnum::BB_2020);

        $objectManager = $this->createMock(EntityManagerInterface::class);

        $playerServiceMock = $this->createMock(PlayerService::class);
        $playerServiceMock->method('coutTotalJoueurs')->willReturn(100_000);

        $equipeService = new EquipeService(
            $objectManager,
            $this->createMock(SettingsService::class),
            $this->createMock(InfosService::class)
        );

        $this->assertEquals(300_000, $equipeService->tvDelEquipe($equipeTest, $playerServiceMock));
    }
}

