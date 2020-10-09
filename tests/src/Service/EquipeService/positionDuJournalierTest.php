<?php

namespace App\Tests\src\Service\EquipeService;


use App\Entity\GameDataPlayers;
use App\Entity\Races;
use App\Entity\Teams;
use App\Service\EquipeService;
use App\Service\InfosService;
use App\Service\SettingsService;
use Doctrine\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class positionDuJournalierTest extends TestCase
{
    /**
     * @test
     */
    public function une_position_est_renvoyee(): void
    {
        $gameDatPlayerTest = new GameDataPlayers();
        $gameDatPlayerTest->setQty('16');
        $gameDatPlayerTest->setPos('halflings');

        $raceTest = new Races();

        $equipeTest = new Teams();
        $equipeTest->setFRace($raceTest);

        $gameDataPlayersRepoMock = $this->createMock(ObjectRepository::class);
        $gameDataPlayersRepoMock->method('findOneBy')->willReturn($gameDatPlayerTest);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($gameDataPlayersRepoMock);

        $equipeService = new EquipeService(
            $objectManager,
            $this->createMock(SettingsService::class),
            $this->createMock(InfosService::class)
        );

        /** @var GameDataPlayers $journalierTest */
        $journalierTest = $equipeService->positionDuJournalier($equipeTest);

        $this->assertEquals('halflings', $journalierTest->getPos() );
    }

    /**
     * @test
     */
    public function une_equipe_mort_vivant_recois_un_zombie(): void
    {
        $gameDatPlayerTest = new GameDataPlayers();
        $gameDatPlayerTest->setQty('16');
        $gameDatPlayerTest->setPos('zombie');

        $raceTest = new Races();
        $raceTest->setName('Morts vivants');

        $equipeTest = new Teams();
        $equipeTest->setFRace($raceTest);

        $gameDataPlayersRepoMock = $this->createMock(ObjectRepository::class);
        $gameDataPlayersRepoMock->method('findOneBy')->willReturn($gameDatPlayerTest);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($gameDataPlayersRepoMock);

        $equipeService = new EquipeService(
            $objectManager,
            $this->createMock(SettingsService::class),
            $this->createMock(InfosService::class)
        );

        /** @var GameDataPlayers $journalierTest */
        $journalierTest = $equipeService->positionDuJournalier($equipeTest);

        $this->assertEquals('zombie', $journalierTest->getPos() );
    }
}