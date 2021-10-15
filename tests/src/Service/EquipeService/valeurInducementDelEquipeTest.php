<?php

namespace App\Tests\src\Service\EquipeService;


use App\Entity\Races;
use App\Entity\RacesBb2020;
use App\Entity\Teams;
use App\Enum\RulesetEnum;
use App\Service\EquipeService;
use App\Service\InducementService;
use App\Service\InfosService;
use App\Service\SettingsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class valeurInducementDelEquipeTest extends KernelTestCase
{

    /**
     * @test
     */
    public function la_valeur_est_bien_calculee_bb2016(): void
    {
        $raceTest = new Races();
        $raceTest->setCostRr(50_000);

        $equipeTest = new Teams;
        $equipeTest->setRerolls(4);
        $equipeTest->setFfBought(6);
        $equipeTest->setFf(4);
        $equipeTest->setCheerleaders(10);
        $equipeTest->setAssCoaches(5);
        $equipeTest->setApothecary(1);
        $equipeTest->setFRace($raceTest);
        $equipeTest->setRuleset(RulesetEnum::BB_2016);

        $objectManager = $this->createMock(EntityManagerInterface::class);

        $equipeService = new EquipeService(
            $objectManager,
            $this->createMock(SettingsService::class),
            $this->createMock(InfosService::class),
            $this->createMock(InducementService::class)
        );

        $retour = [
            'rerolls'=> 200_000,
            'pop'=> 100_000,
            'asscoaches'=> 50_000,
            'cheerleader'=> 100_000,
            'apo'=> 50_000,
            'total'=> 500_000
        ];

        $this->assertEquals($retour,$equipeService->valeurInducementDelEquipe($equipeTest));
    }

    /**
     * @test
     */
    public function la_valeur_est_bien_calculee_bb2020(): void
    {
        $raceTest = new RacesBb2020();
        $raceTest->setCostRr(50_000);

        $equipeTest = new Teams;
        $equipeTest->setRerolls(4);
        $equipeTest->setFfBought(6);
        $equipeTest->setFf(4);
        $equipeTest->setCheerleaders(10);
        $equipeTest->setAssCoaches(5);
        $equipeTest->setApothecary(1);
        $equipeTest->setRace($raceTest);
        $equipeTest->setRuleset(RulesetEnum::BB_2020);

        $objectManager = $this->createMock(EntityManagerInterface::class);

        $equipeService = new EquipeService(
            $objectManager,
            $this->createMock(SettingsService::class),
            $this->createMock(InfosService::class),
            $this->createMock(InducementService::class)
        );

        $retour = [
            'rerolls'=> 200_000,
            'pop'=> 0,
            'asscoaches'=> 50_000,
            'cheerleader'=> 100_000,
            'apo'=> 50_000,
            'total'=> 400_000
        ];

        $this->assertEquals($retour,$equipeService->valeurInducementDelEquipe($equipeTest));
    }
}