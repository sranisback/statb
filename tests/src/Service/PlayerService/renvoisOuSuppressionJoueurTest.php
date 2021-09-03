<?php

namespace App\Tests\src\Service\PlayerService;

use App\Entity\GameDataPlayers;
use App\Entity\GameDataPlayersBb2020;
use App\Entity\GameDataSkills;
use App\Entity\GameDataSkillsBb2020;
use App\Entity\MatchData;
use App\Entity\Players;
use App\Entity\PlayersSkills;
use App\Entity\Teams;
use App\Enum\RulesetEnum;
use App\Service\EquipeService;
use App\Service\InfosService;
use App\Service\MatchDataService;
use App\Service\PlayerService;
use Doctrine\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class renvoisOuSuppressionJoueurTest extends TestCase
{
    /**
     * @test
     */
    public function le_joueur_est_renvoye_bb2016(): void
    {
        $positionTest = new GameDataPlayers();
        $positionTest->setCost(50_000);

        $equipeTest = new Teams();
        $equipeTest->setTreasury(50_000);

        $playerTest = new Players();
        $playerTest->setFPos($positionTest);
        $playerTest->setOwnedByTeam($equipeTest);
        $playerTest->setRuleset(RulesetEnum::BB_2016);

        $matchDataTest = new MatchData();
        $matchDataTest->setFPlayer($playerTest);

        $matchDataRepoMock = $this->getMockBuilder(Players::class)
            ->addMethods(['listeDesMatchsdUnJoueur'])
            ->getMock();
        $matchDataRepoMock->method('listeDesMatchsdUnJoueur')->willReturn(
            [$matchDataTest]
        );

        $playersSkillsRepoMock = $this->createMock(ObjectRepository::class);
        $playersSkillsRepoMock->method('findBy')->willReturn([]);

        $gameDataSkillsTest = $this->createMock(GameDataSkills::class);
        $gameDataSkillsTest->method('getName')->willReturn('Disposable');
        $gameDataSkillsTest->method('getSkillId')->willReturn(1);

        $gameDataSkillRepoMock = $this->getMockBuilder(Players::class)
            ->addMethods(['findOneBy'])
            ->getMock();
        $gameDataSkillRepoMock->method('findOneBy')->willReturn($gameDataSkillsTest);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->will(
            $this->returnCallback(
                function ($entityName) use ($matchDataRepoMock, $playersSkillsRepoMock,$gameDataSkillRepoMock) {
                    if ($entityName === MatchData::class) {
                        return $matchDataRepoMock;
                    }

                    if ($entityName === PlayersSkills::class) {
                        return $playersSkillsRepoMock;
                    }

                    if ($entityName === GameDataSkills::class) {
                        return $gameDataSkillRepoMock;
                    }
                    return true;
                }
            )
        );

        $equipeServiceMock = $this->createMock(EquipeService::class);
        $equipeServiceMock->method('tvDelEquipe')->willReturnOnConsecutiveCalls(1_000_000);

        $playerServiceTest = new PlayerService(
            $objectManager,
            $equipeServiceMock,
            $this->createMock(MatchDataService::class),
            $this->createMock(InfosService::class)
        );

        $reponseTest = [
            'reponse' => 'sld',
            'tv' => 1_000,
            'tresor' => 50_000,
            'playercost' => 50_000,
        ];

        $this->assertEquals($reponseTest, $playerServiceTest->renvoisOuSuppressionJoueur($playerTest));
    }

    /**
     * @test
     */
    public function le_joueur_est_renvoye_bb2020(): void
    {
        $positionTest = new GameDataPlayersBb2020();
        $positionTest->setCost(50_000);

        $equipeTest = new Teams();
        $equipeTest->setTreasury(50_000);

        $playerTest = new Players();
        $playerTest->setFPosBb2020($positionTest);
        $playerTest->setOwnedByTeam($equipeTest);
        $playerTest->setRuleset(RulesetEnum::BB_2020);

        $matchDataTest = new MatchData();
        $matchDataTest->setFPlayer($playerTest);

        $matchDataRepoMock = $this->getMockBuilder(Players::class)
            ->addMethods(['listeDesMatchsdUnJoueur'])
            ->getMock();
        $matchDataRepoMock->method('listeDesMatchsdUnJoueur')->willReturn(
            [$matchDataTest]
        );

        $playersSkillsRepoMock = $this->createMock(ObjectRepository::class);
        $playersSkillsRepoMock->method('findBy')->willReturn([]);

        $gameDataSkillsTest = $this->createMock(GameDataSkillsBb2020::class);
        $gameDataSkillsTest->method('getName')->willReturn('Disposable');
        $gameDataSkillsTest->method('getId')->willReturn(1);

        $gameDataSkillRepoMock = $this->getMockBuilder(Players::class)
            ->addMethods(['findOneBy'])
            ->getMock();
        $gameDataSkillRepoMock->method('findOneBy')->willReturn($gameDataSkillsTest);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->will(
            $this->returnCallback(
                function ($entityName) use ($matchDataRepoMock, $playersSkillsRepoMock,$gameDataSkillRepoMock) {
                    if ($entityName === MatchData::class) {
                        return $matchDataRepoMock;
                    }

                    if ($entityName === PlayersSkills::class) {
                        return $playersSkillsRepoMock;
                    }

                    if ($entityName === GameDataSkillsBb2020::class) {
                        return $gameDataSkillRepoMock;
                    }
                    return true;
                }
            )
        );

        $equipeServiceMock = $this->createMock(EquipeService::class);
        $equipeServiceMock->method('tvDelEquipe')->willReturnOnConsecutiveCalls(1_000_000);

        $playerServiceTest = new PlayerService(
            $objectManager,
            $equipeServiceMock,
            $this->createMock(MatchDataService::class),
            $this->createMock(InfosService::class)
        );

        $reponseTest = [
            'reponse' => 'sld',
            'tv' => 1_000,
            'tresor' => 50_000,
            'playercost' => 50_000,
        ];

        $this->assertEquals($reponseTest, $playerServiceTest->renvoisOuSuppressionJoueur($playerTest));
    }

    /**
     * @test
     */
    public function le_joueur_est_supprime_bb2016(): void
    {
        $positionTest = new GameDataPlayers();
        $positionTest->setCost(50_000);

        $equipeTest = new Teams();
        $equipeTest->setTreasury(50_000);

        $playerTest = new Players();
        $playerTest->setFPos($positionTest);
        $playerTest->setOwnedByTeam($equipeTest);
        $playerTest->setJournalier(false);
        $playerTest->setRuleset(RulesetEnum::BB_2016);

        $matchDataRepoMock = $this->getMockBuilder(Players::class)
            ->addMethods(['listeDesMatchsdUnJoueur'])
            ->getMock();
        $matchDataRepoMock->method('listeDesMatchsdUnJoueur')->willReturn(
            []
        );

        $playersSkillsRepoMock = $this->createMock(ObjectRepository::class);
        $playersSkillsRepoMock->method('findBy')->willReturn([]);

        $gameDataSkillsTest = $this->createMock(GameDataSkills::class);
        $gameDataSkillsTest->method('getName')->willReturn('Disposable');
        $gameDataSkillsTest->method('getSkillId')->willReturn(1);

        $gameDataSkillRepoMock = $this->getMockBuilder(Players::class)
            ->addMethods(['findOneBy'])
            ->getMock();
        $gameDataSkillRepoMock->method('findOneBy')->willReturn($gameDataSkillsTest);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->will(
            $this->returnCallback(
                function ($entityName) use ($matchDataRepoMock, $playersSkillsRepoMock, $gameDataSkillRepoMock) {
                    if ($entityName === MatchData::class) {
                        return $matchDataRepoMock;
                    }

                    if ($entityName === PlayersSkills::class) {
                        return $playersSkillsRepoMock;
                    }

                    if ($entityName === GameDataSkills::class) {
                        return $gameDataSkillRepoMock;
                    }
                    return true;
                }
            )
        );

        $equipeServiceMock = $this->createMock(EquipeService::class);
        $equipeServiceMock->method('tvDelEquipe')->willReturn(1_000_000);

        $playerServiceTest = new PlayerService(
            $objectManager,
            $equipeServiceMock,
            $this->createMock(MatchDataService::class),
            $this->createMock(InfosService::class)
        );

        $reponseTest = [
            'reponse' => 'rm',
            'tv' => 1_000,
            'tresor' => 100_000,
            'playercost' => 50_000,
        ];

        $this->assertEquals($reponseTest, $playerServiceTest->renvoisOuSuppressionJoueur($playerTest));
    }

    /**
     * @test
     */
    public function le_joueur_est_supprime_bb2020(): void
    {
        $positionTest = new GameDataPlayersBb2020();
        $positionTest->setCost(50_000);

        $equipeTest = new Teams();
        $equipeTest->setTreasury(50_000);

        $playerTest = new Players();
        $playerTest->setFPosBb2020($positionTest);
        $playerTest->setOwnedByTeam($equipeTest);
        $playerTest->setJournalier(false);
        $playerTest->setRuleset(RulesetEnum::BB_2020);

        $matchDataRepoMock = $this->getMockBuilder(Players::class)
            ->addMethods(['listeDesMatchsdUnJoueur'])
            ->getMock();
        $matchDataRepoMock->method('listeDesMatchsdUnJoueur')->willReturn(
            []
        );

        $playersSkillsRepoMock = $this->createMock(ObjectRepository::class);
        $playersSkillsRepoMock->method('findBy')->willReturn([]);

        $gameDataSkillsTest = $this->createMock(GameDataSkillsBb2020::class);
        $gameDataSkillsTest->method('getName')->willReturn('Disposable');
        $gameDataSkillsTest->method('getId')->willReturn(1);

        $gameDataSkillRepoMock = $this->getMockBuilder(Players::class)
            ->addMethods(['findOneBy'])
            ->getMock();
        $gameDataSkillRepoMock->method('findOneBy')->willReturn($gameDataSkillsTest);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->will(
            $this->returnCallback(
                function ($entityName) use ($matchDataRepoMock, $playersSkillsRepoMock, $gameDataSkillRepoMock) {
                    if ($entityName === MatchData::class) {
                        return $matchDataRepoMock;
                    }

                    if ($entityName === PlayersSkills::class) {
                        return $playersSkillsRepoMock;
                    }

                    if ($entityName === GameDataSkillsBb2020::class) {
                        return $gameDataSkillRepoMock;
                    }
                    return true;
                }
            )
        );

        $equipeServiceMock = $this->createMock(EquipeService::class);
        $equipeServiceMock->method('tvDelEquipe')->willReturn(1_000_000);

        $playerServiceTest = new PlayerService(
            $objectManager,
            $equipeServiceMock,
            $this->createMock(MatchDataService::class),
            $this->createMock(InfosService::class)
        );

        $reponseTest = [
            'reponse' => 'rm',
            'tv' => 1_000,
            'tresor' => 100_000,
            'playercost' => 50_000,
        ];

        $this->assertEquals($reponseTest, $playerServiceTest->renvoisOuSuppressionJoueur($playerTest));
    }
}