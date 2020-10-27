<?php

namespace App\Tests\src\Service\EquipeService;

use App\Entity\Matches;
use App\Entity\Teams;
use App\Service\EquipeService;
use App\Service\InfosService;
use App\Service\SettingsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class resultatDuMatchTest extends KernelTestCase
{
    /**
     * @test
     */
    public function une_victoire(): void
    {
        $equipeTest = new Teams();

        $matchTest = new Matches();
        $matchTest->setTeam1($equipeTest);
        $matchTest->setTeam1Score(2);
        $matchTest->setTeam2Score(0);

        $objectManager = $this->createMock(EntityManagerInterface::class);

        $equipeServiceTest = new EquipeService(
            $objectManager,
            $this->createMock(SettingsService::class),
            $this->createMock(InfosService::class)
        );

        $resultatAttendu = [
            'win' => 1,
            'loss' => 0,
            'draw' => 0
        ];

        $this->assertEquals($resultatAttendu, $equipeServiceTest->resultatDuMatch($equipeTest, $matchTest));
    }

    /**
     * @test
     */
    public function un_defaite(): void
    {
        $equipeTest = new Teams();

        $matchTest = new Matches();
        $matchTest->setTeam1($equipeTest);
        $matchTest->setTeam1Score(0);
        $matchTest->setTeam2Score(2);

        $objectManager = $this->createMock(EntityManagerInterface::class);

        $equipeServiceTest = new EquipeService(
            $objectManager,
            $this->createMock(SettingsService::class),
            $this->createMock(InfosService::class)
        );

        $resultatAttendu = [
            'win' => 0,
            'loss' => 1,
            'draw' => 0
        ];

        $this->assertEquals($resultatAttendu, $equipeServiceTest->resultatDuMatch($equipeTest, $matchTest));
    }

    /**
     * @test
     */
    public function une_egalite(): void
    {
        $equipeTest = new Teams();

        $matchTest = new Matches();
        $matchTest->setTeam1($equipeTest);
        $matchTest->setTeam1Score(2);
        $matchTest->setTeam2Score(2);

        $objectManager = $this->createMock(EntityManagerInterface::class);

        $equipeServiceTest = new EquipeService(
            $objectManager,
            $this->createMock(SettingsService::class),
            $this->createMock(InfosService::class)
        );

        $resultatAttendu = [
            'win' => 0,
            'loss' => 0,
            'draw' => 1
        ];

        $this->assertEquals($resultatAttendu, $equipeServiceTest->resultatDuMatch($equipeTest, $matchTest));
    }
}