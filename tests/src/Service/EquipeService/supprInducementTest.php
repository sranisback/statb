<?php

namespace App\Tests\src\Service\EquipeService;


use App\Entity\Matches;
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

class supprInducementTest extends KernelTestCase
{
    /**
     * @test
     */
    public function les_inducements_rendent_l_argent_si_pas_de_match_bb2016(): void
    {
        $raceTest = new Races();
        $raceTest->setCostRr(50_000);

        $equipeTest = new Teams();
        $equipeTest->setFRace($raceTest);
        $equipeTest->setRerolls(2);
        $equipeTest->setTreasury(0);
        $equipeTest->setRuleset(RulesetEnum::BB_2016);

        $matchRepoMock = $this->getMockBuilder(Matches::class)->addMethods(['listeDesMatchs'])->getMock();
        $matchRepoMock->method('listeDesMatchs')->willReturn([]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchRepoMock);

        $equipeServiceTest = new EquipeService(
            $objectManager,
            $this->createMock(SettingsService::class),
            $this->createMock(InfosService::class)
        );

        $resultatAttendu = [
            'inducost' => 50_000,
            'nbr' => 1
        ];

        $this->assertEquals($resultatAttendu, $equipeServiceTest->supprInducement(
            $equipeTest,
            'rr',
            $this->createMock(PlayerService::class)
        ));

        $this->assertEquals(50_000, $equipeTest->getTreasury());
        $this->assertEquals(1, $equipeTest->getRerolls());
    }

    /**
     * @test
     */
    public function les_inducements_rendent_l_argent_si_pas_de_match_bb2020(): void
    {
        $raceTest = new RacesBb2020();
        $raceTest->setCostRr(50_000);

        $equipeTest = new Teams();
        $equipeTest->setRace($raceTest);
        $equipeTest->setRerolls(2);
        $equipeTest->setTreasury(0);
        $equipeTest->setRuleset(RulesetEnum::BB_2020);

        $matchRepoMock = $this->getMockBuilder(Matches::class)->addMethods(['listeDesMatchs'])->getMock();
        $matchRepoMock->method('listeDesMatchs')->willReturn([]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchRepoMock);

        $equipeServiceTest = new EquipeService(
            $objectManager,
            $this->createMock(SettingsService::class),
            $this->createMock(InfosService::class)
        );

        $resultatAttendu = [
            'inducost' => 50_000,
            'nbr' => 1
        ];

        $this->assertEquals($resultatAttendu, $equipeServiceTest->supprInducement(
            $equipeTest,
            'rr',
            $this->createMock(PlayerService::class)
        ));

        $this->assertEquals(50_000, $equipeTest->getTreasury());
        $this->assertEquals(1, $equipeTest->getRerolls());
    }

    /**
     * @test
     */
    public function les_inducements_ne_rendent_pas_l_argent_si_match_bb2016(): void
    {
        $raceTest = new Races();
        $raceTest->setCostRr(50_000);

        $equipeTest = new Teams();
        $equipeTest->setFRace($raceTest);
        $equipeTest->setRerolls(2);
        $equipeTest->setTreasury(0);
        $equipeTest->setRuleset(RulesetEnum::BB_2016);

        $matchTest = new Matches();

        $matchRepoMock = $this->getMockBuilder(Matches::class)->setMethods(['listeDesMatchs'])->getMock();
        $matchRepoMock->method('listeDesMatchs')->willReturn([$matchTest]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchRepoMock);

        $equipeServiceTest = new EquipeService(
            $objectManager,
            $this->createMock(SettingsService::class),
            $this->createMock(InfosService::class)
        );

        $resultatAttendu = [
            'inducost' => 50_000,
            'nbr' => 1
        ];

        $this->assertEquals($resultatAttendu, $equipeServiceTest->supprInducement(
            $equipeTest,
            'rr',
            $this->createMock(PlayerService::class)
        ));

        $this->assertEquals(0, $equipeTest->getTreasury());
        $this->assertEquals(1, $equipeTest->getRerolls());
    }

    /**
     * @test
     */
    public function les_inducements_ne_rendent_pas_l_argent_si_match_bb2020(): void
    {
        $raceTest = new RacesBb2020();
        $raceTest->setCostRr(50_000);

        $equipeTest = new Teams();
        $equipeTest->setRace($raceTest);
        $equipeTest->setRerolls(2);
        $equipeTest->setTreasury(0);
        $equipeTest->setRuleset(RulesetEnum::BB_2020);

        $matchTest = new Matches();

        $matchRepoMock = $this->getMockBuilder(Matches::class)->setMethods(['listeDesMatchs'])->getMock();
        $matchRepoMock->method('listeDesMatchs')->willReturn([$matchTest]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchRepoMock);

        $equipeServiceTest = new EquipeService(
            $objectManager,
            $this->createMock(SettingsService::class),
            $this->createMock(InfosService::class)
        );

        $resultatAttendu = [
            'inducost' => 50_000,
            'nbr' => 1
        ];

        $this->assertEquals($resultatAttendu, $equipeServiceTest->supprInducement(
            $equipeTest,
            'rr',
            $this->createMock(PlayerService::class)
        ));

        $this->assertEquals(0, $equipeTest->getTreasury());
        $this->assertEquals(1, $equipeTest->getRerolls());
    }
}