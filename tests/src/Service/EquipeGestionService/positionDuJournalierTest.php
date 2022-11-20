<?php

namespace App\Tests\src\Service\EquipeGestionService;


use App\Entity\GameDataPlayers;
use App\Entity\GameDataPlayersBb2020;
use App\Entity\Races;
use App\Entity\RacesBb2020;
use App\Entity\Teams;
use App\Enum\RulesetEnum;
use App\Service\EquipeGestionService;
use App\Service\InducementService;
use App\Service\InfosService;
use App\Service\SettingsService;
use App\Tests\src\TestServiceFactory\EquipeGestionServiceTestFactory;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;

class positionDuJournalierTest extends TestCase
{
    /**
     * @test
     */
    public function une_position_est_renvoyee_bb2016(): void
    {
        $gameDatPlayerTest = new GameDataPlayers();
        $gameDatPlayerTest->setQty('16');
        $gameDatPlayerTest->setPos('halflings');

        $raceTest = new Races();

        $equipeTest = new Teams();
        $equipeTest->setFRace($raceTest);
        $equipeTest->setRuleset(RulesetEnum::BB_2016);

        $gameDataPlayersRepoMock = $this->createMock(ObjectRepository::class);
        $gameDataPlayersRepoMock->method('findOneBy')->willReturn($gameDatPlayerTest);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($gameDataPlayersRepoMock);

        $equipeGestionService = (new EquipeGestionServiceTestFactory())->getInstance(
            $objectManager
        );

        /** @var GameDataPlayers $journalierTest */
        $journalierTest = $equipeGestionService->positionDuJournalier($equipeTest);

        $this->assertEquals('halflings', $journalierTest->getPos() );
    }

    /**
     * @test
     */
    public function une_position_0_16_est_renvoyee_bb2020(): void
    {
        $gameDatPlayerTest = new GameDataPlayersBb2020();
        $gameDatPlayerTest->setQty('16');
        $gameDatPlayerTest->setPos('halflings');

        $raceTest = new RacesBb2020();

        $equipeTest = new Teams();
        $equipeTest->setRace($raceTest);
        $equipeTest->setRuleset(RulesetEnum::BB_2020);

        $gameDataPlayersRepoMock = $this->createMock(ObjectRepository::class);
        $gameDataPlayersRepoMock->method('findOneBy')->willReturnOnConsecutiveCalls(null,$gameDatPlayerTest);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($gameDataPlayersRepoMock);

        $equipeGestionService = (new EquipeGestionServiceTestFactory())->getInstance(
            $objectManager
        );

        /** @var GameDataPlayers $journalierTest */
        $journalierTest = $equipeGestionService->positionDuJournalier($equipeTest);

        $this->assertEquals('halflings', $journalierTest->getPos() );
    }

    /**
     * @test
     */
    public function une_position_0_12_est_renvoyee_bb2020(): void
    {
        $gameDatPlayerTest = new GameDataPlayersBb2020();
        $gameDatPlayerTest->setQty('12');
        $gameDatPlayerTest->setPos('halflings');

        $raceTest = new RacesBb2020();

        $equipeTest = new Teams();
        $equipeTest->setRace($raceTest);
        $equipeTest->setRuleset(RulesetEnum::BB_2020);

        $gameDataPlayersRepoMock = $this->createMock(ObjectRepository::class);
        $gameDataPlayersRepoMock->method('findOneBy')->willReturn($gameDatPlayerTest);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($gameDataPlayersRepoMock);

        $equipeGestionService = (new EquipeGestionServiceTestFactory())->getInstance(
            $objectManager
        );

        /** @var GameDataPlayers $journalierTest */
        $journalierTest = $equipeGestionService->positionDuJournalier($equipeTest);

        $this->assertEquals('halflings', $journalierTest->getPos() );
    }

    /**
     * @test
     */
    public function une_equipe_mort_vivant_recoit_un_zombie_bb2016(): void
    {
        $gameDatPlayerTest = new GameDataPlayers();
        $gameDatPlayerTest->setQty('16');
        $gameDatPlayerTest->setPos('zombie');

        $raceTest = new Races();
        $raceTest->setName('Morts vivants');

        $equipeTest = new Teams();
        $equipeTest->setFRace($raceTest);
        $equipeTest->setRuleset(RulesetEnum::BB_2016);

        $gameDataPlayersRepoMock = $this->createMock(ObjectRepository::class);
        $gameDataPlayersRepoMock->method('findOneBy')->willReturn($gameDatPlayerTest);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($gameDataPlayersRepoMock);

        $equipeGestionService = (new EquipeGestionServiceTestFactory())->getInstance(
            $objectManager
        );

        /** @var GameDataPlayers $journalierTest */
        $journalierTest = $equipeGestionService->positionDuJournalier($equipeTest);

        $this->assertEquals('zombie', $journalierTest->getPos() );
    }

    /**
     * @test
     */
    public function une_equipe_mort_vivant_recoit_un_zombie_bb2020(): void
    {
        $gameDatPlayerTest = new GameDataPlayersBb2020();
        $gameDatPlayerTest->setQty('16');
        $gameDatPlayerTest->setPos('zombie');

        $raceTest = new RacesBb2020();
        $raceTest->setName('Morts vivants');

        $equipeTest = new Teams();
        $equipeTest->setRace($raceTest);
        $equipeTest->setRuleset(RulesetEnum::BB_2020);

        $gameDataPlayersRepoMock = $this->createMock(ObjectRepository::class);
        $gameDataPlayersRepoMock->method('findOneBy')->willReturn($gameDatPlayerTest);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($gameDataPlayersRepoMock);

        $equipeGestionService = (new EquipeGestionServiceTestFactory())->getInstance(
            $objectManager
        );

        /** @var GameDataPlayers $journalierTest */
        $journalierTest = $equipeGestionService->positionDuJournalier($equipeTest);

        $this->assertEquals('zombie', $journalierTest->getPos() );
    }
}