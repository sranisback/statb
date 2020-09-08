<?php

namespace App\src\Service\EquipeService;

use App\Entity\Matches;
use App\Entity\Races;
use App\Entity\Teams;
use App\Service\ClassementService;
use App\Service\EquipeService;
use App\Service\PlayerService;
use App\Service\SettingsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AjoutInducementTest extends KernelTestCase
{
     /**
     * @test
     */
    public function le_cout_des_rr_change_quand_l_equipe_a_un_match()
    {
        $raceTest = new Races();
        $raceTest->setCostRr(50000);

        $equipeTest = new Teams();
        $equipeTest->setFRace($raceTest);
        $equipeTest->setTreasury(100000);
        $equipeTest->setRerolls(0);

        $matchTest = new Matches();

        $matchRepoMock = $this->getMockBuilder(Matches::class)->setMethods(['listeDesMatchs'])->getMock();
        $matchRepoMock->method('listeDesMatchs')->willReturn([$matchTest]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchRepoMock);

        $equipeServiceTest = new EquipeService(
            $objectManager,
            $this->createMock(SettingsService::class),
            $this->createMock(ClassementService::class)
        );

        $resultatAttendu = [
            'inducost' => '100000',
            'nbr' => 1
        ];

        $this->assertEquals($resultatAttendu, $equipeServiceTest->ajoutInducement(
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
    public function la_pop_ne_monte_plus_apres_un_match()
    {
        $raceTest = new Races();
        $raceTest->setCostRr(50000);

        $equipeTest = new Teams();
        $equipeTest->setFRace($raceTest);
        $equipeTest->setTreasury(10000);
        $equipeTest->setFfBought(2);

        $matchTest = new Matches();

        $matchRepoMock = $this->getMockBuilder(Matches::class)->setMethods(['listeDesMatchs'])->getMock();
        $matchRepoMock->method('listeDesMatchs')->willReturn([$matchTest]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchRepoMock);

        $equipeServiceTest = new EquipeService(
            $objectManager,
            $this->createMock(SettingsService::class),
            $this->createMock(ClassementService::class)
        );

        $resultatAttendu = [
            'inducost' => 0,
            'nbr' => 2
        ];

        $this->assertEquals($resultatAttendu, $equipeServiceTest->ajoutInducement(
            $equipeTest,
            'pop',
            $this->createMock(PlayerService::class)
        ));

        $this->assertEquals(2, $equipeTest->getFfBought());
        $this->assertEquals(10000, $equipeTest->getTreasury());
    }

    /**
     * @test
     */
    public function le_cout_des_rr_avant_matchs()
    {
        $raceTest = new Races();
        $raceTest->setCostRr(50000);

        $equipeTest = new Teams();
        $equipeTest->setFRace($raceTest);
        $equipeTest->setTreasury(50000);
        $equipeTest->setRerolls(0);

        $matchRepoMock = $this->getMockBuilder(Matches::class)->setMethods(['listeDesMatchs'])->getMock();
        $matchRepoMock->method('listeDesMatchs')->willReturn([]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchRepoMock);

        $equipeServiceTest = new EquipeService(
            $objectManager,
            $this->createMock(SettingsService::class),
            $this->createMock(ClassementService::class)
        );

        $resultatAttendu = [
            'inducost' => '50000',
            'nbr' => 1
        ];

        $this->assertEquals($resultatAttendu, $equipeServiceTest->ajoutInducement(
            $equipeTest,
            'rr',
            $this->createMock(PlayerService::class)
        ));

        $this->assertEquals(0, $equipeTest->getTreasury());
        $this->assertEquals(1, $equipeTest->getRerolls());
    }

}