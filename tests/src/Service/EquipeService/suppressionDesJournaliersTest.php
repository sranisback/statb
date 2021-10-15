<?php

namespace App\Tests\src\Service\EquipeService;

use App\Entity\Players;
use App\Entity\Teams;
use App\Service\EquipeService;
use App\Service\InducementService;
use App\Service\InfosService;
use App\Service\SettingsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class suppressionDesJournaliersTest extends KernelTestCase
{
    /**
     * @test
     */
    public function un_journalier_est_vendu(): void
    {
        $joueurTest0 = new Players();

        $joueurRepoMock = $this->getMockBuilder(Players::class)
            ->addMethods(['listeDesJournaliersDeLequipe'])
            ->getMock();
        $joueurRepoMock->method('listeDesJournaliersDeLequipe')->willReturn(
            [
                $joueurTest0
            ]
        );

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($joueurRepoMock);

        $equipeService = new EquipeService(
            $objectManager,
            $this->createMock(SettingsService::class),
            $this->createMock(InfosService::class),
            $this->createMock(InducementService::class)
        );

        $this->assertEquals(
            1,
            $equipeService->suppressionDesJournaliers(
                1,
                $this->createMock(Teams::class)
            )
        );
    }

    /**
     * @test
     */
    public function il_y_a_deux_journaliers_mais_un_seul_a_vendre(): void
    {
        $joueurTest0 = new Players();
        $joueurTest1 = new Players();

        $joueurRepoMock = $this->getMockBuilder(Players::class)
            ->addMethods(['listeDesJournaliersDeLequipe'])
            ->getMock();
        $joueurRepoMock->method('listeDesJournaliersDeLequipe')->willReturn(
            [
                $joueurTest0,
                $joueurTest1
            ]
        );

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($joueurRepoMock);

        $equipeService = new EquipeService(
            $objectManager,
            $this->createMock(SettingsService::class),
            $this->createMock(InfosService::class),
            $this->createMock(InducementService::class)
        );

        $this->assertEquals(
            1,
            $equipeService->suppressionDesJournaliers(
                1,
                $this->createMock(Teams::class)
            )
        );
    }

    /**
     * @test
     */
    public function il_y_en_a_plusieurs_a_vendre(): void
    {
        $joueurTest0 = new Players();
        $joueurTest1 = new Players();
        $joueurTest2 = new Players();
        $joueurTest3 = new Players();

        $joueurRepoMock = $this->getMockBuilder(Players::class)
            ->addMethods(['listeDesJournaliersDeLequipe'])
            ->getMock();
        $joueurRepoMock->method('listeDesJournaliersDeLequipe')->willReturn(
            [
                $joueurTest0,
                $joueurTest1,
                $joueurTest2,
                $joueurTest3
            ]
        );

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($joueurRepoMock);

        $equipeService = new EquipeService(
            $objectManager,
            $this->createMock(SettingsService::class),
            $this->createMock(InfosService::class),
            $this->createMock(InducementService::class)
        );

        $this->assertEquals(
            4,
            $equipeService->suppressionDesJournaliers(
                4,
                $this->createMock(Teams::class)
            )
        );
    }

    /**
     * @test
     */
    public function certains_sont_en_attente_xp(): void
    {
        $joueurTest0 = new Players();
        $joueurTest1 = new Players();
        $joueurTest1->setStatus(9);
        $joueurTest2 = new Players();
        $joueurTest2->setStatus(9);
        $joueurTest3 = new Players();

        $joueurRepoMock = $this->getMockBuilder(Players::class)
            ->addMethods(['listeDesJournaliersDeLequipe'])
            ->getMock();
        $joueurRepoMock->method('listeDesJournaliersDeLequipe')->willReturn(
            [
                $joueurTest0,
                $joueurTest1,
                $joueurTest2,
                $joueurTest3
            ]
        );

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($joueurRepoMock);

        $equipeService = new EquipeService(
            $objectManager,
            $this->createMock(SettingsService::class),
            $this->createMock(InfosService::class),
            $this->createMock(InducementService::class)
        );

        $this->assertEquals(
            2,
            $equipeService->suppressionDesJournaliers(
                4,
                $this->createMock(Teams::class)
            )
        );
    }
}