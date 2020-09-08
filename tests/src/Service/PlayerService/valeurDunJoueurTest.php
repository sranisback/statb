<?php

namespace App\Tests\src\Service\PlayerService;


use App\Entity\GameDataPlayers;
use App\Entity\GameDataSkills;
use App\Entity\Players;
use App\Entity\PlayersSkills;
use App\Service\EquipeService;
use App\Service\MatchDataService;
use App\Service\PlayerService;
use Doctrine\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class valeurDunJoueurTest extends TestCase
{
    /**
     * @test
     */
    public function la_valeur_de_base_est_bien_retournee(): void
    {
        $positionTest = new GameDataPlayers();
        $positionTest->setCost(50_000);

        $joueurTest = new Players();
        $joueurTest->setFPos($positionTest);

        $playerSkillRepoMock = $this->createMock(ObjectRepository::class);
        $playerSkillRepoMock->method('findBy')->willReturn([]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($playerSkillRepoMock);

        $playerServiceTest = new PlayerService(
            $objectManager,
            $this->createMock(EquipeService::class),
            $this->createMock(MatchDataService::class)
        );

        $this->assertEquals(50_000, $playerServiceTest->valeurDunJoueur($joueurTest));
    }

    /**
     * @test
     */
    public function les_comps_simples_sont_bien_comptees(): void
    {
        $positionTest = new GameDataPlayers();
        $positionTest->setCost(50_000);

        $joueurTest = new Players();
        $joueurTest->setFPos($positionTest);

        $gameDataSkillTest = new GameDataSkills();
        $gameDataSkillTest->setName('test skill');

        $playerSkillTest = new PlayersSkills();
        $playerSkillTest->setType('N');
        $playerSkillTest->setFSkill($gameDataSkillTest);

        $playerSkillRepoMock = $this->createMock(ObjectRepository::class);
        $playerSkillRepoMock->method('findBy')->willReturn([$playerSkillTest]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($playerSkillRepoMock);

        $playerServiceTest = new PlayerService(
            $objectManager,
            $this->createMock(EquipeService::class),
            $this->createMock(MatchDataService::class)
        );

        $this->assertEquals(70_000, $playerServiceTest->valeurDunJoueur($joueurTest));
    }

    /**
     * @test
     */
    public function les_comps_doubles_sont_bien_comptees(): void
    {
        $positionTest = new GameDataPlayers();
        $positionTest->setCost(50_000);

        $joueurTest = new Players();
        $joueurTest->setFPos($positionTest);

        $gameDataSkillTest = new GameDataSkills();
        $gameDataSkillTest->setName('test skill');

        $playerSkillTest = new PlayersSkills();
        $playerSkillTest->setType('D');
        $playerSkillTest->setFSkill($gameDataSkillTest);

        $playerSkillRepoMock = $this->createMock(ObjectRepository::class);
        $playerSkillRepoMock->method('findBy')->willReturn([$playerSkillTest]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($playerSkillRepoMock);

        $playerServiceTest = new PlayerService(
            $objectManager,
            $this->createMock(EquipeService::class),
            $this->createMock(MatchDataService::class)
        );

        $this->assertEquals(80_000, $playerServiceTest->valeurDunJoueur($joueurTest));
    }

    /**
     * @test
     */
    public function les_augmentations_de_stats_sont_bien_comptees(): void
    {
        $positionTest = new GameDataPlayers();
        $positionTest->setCost(50_000);

        $joueurTest = new Players();
        $joueurTest->setFPos($positionTest);
        $joueurTest->setAchMa(1);
        $joueurTest->setAchAg(1);
        $joueurTest->setAchSt(1);
        $joueurTest->setAchAv(1);

        $playerSkillRepoMock = $this->createMock(ObjectRepository::class);
        $playerSkillRepoMock->method('findBy')->willReturn([]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($playerSkillRepoMock);

        $playerServiceTest = new PlayerService(
            $objectManager,
            $this->createMock(EquipeService::class),
            $this->createMock(MatchDataService::class)
        );

        $this->assertEquals(200_000, $playerServiceTest->valeurDunJoueur($joueurTest));
    }

    /**
     * @test
     */
    public function le_joueur_a_plusieurs_type_de_comp(): void
    {
        $positionTest = new GameDataPlayers();
        $positionTest->setCost(50_000);

        $joueurTest = new Players();
        $joueurTest->setFPos($positionTest);

        $gameDataSkillTest = new GameDataSkills();
        $gameDataSkillTest->setName('test skill');

        $playerSkillTest0 = new PlayersSkills();
        $playerSkillTest0->setType('N');
        $playerSkillTest0->setFSkill($gameDataSkillTest);

        $playerSkillTest1 = new PlayersSkills();
        $playerSkillTest1->setType('D');
        $playerSkillTest1->setFSkill($gameDataSkillTest);

        $playerSkillRepoMock = $this->createMock(ObjectRepository::class);
        $playerSkillRepoMock->method('findBy')->willReturn([$playerSkillTest0,$playerSkillTest1]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($playerSkillRepoMock);

        $playerServiceTest = new PlayerService(
            $objectManager,
            $this->createMock(EquipeService::class),
            $this->createMock(MatchDataService::class)
        );

        $this->assertEquals(100_000, $playerServiceTest->valeurDunJoueur($joueurTest));
    }

    /**
     * @test
     */
    public function le_joueur_a_disposable()
    {
        $gameDataSkillsTest = $this->createMock(GameDataSkills::class);
        $gameDataSkillsTest->method('getName')->willReturn('Disposable');
        $gameDataSkillsTest->method('getSkillId')->willReturn(1);

        $positionTest = new GameDataPlayers();
        $positionTest->setCost(50_000);
        $positionTest->setSkills(1);

        $joueurTest = new Players();
        $joueurTest->setFPos($positionTest);

        $playerSkillRepoMock = $this->createMock(ObjectRepository::class);
        $playerSkillRepoMock->method('findBy')->willReturn([]);

        $gameDataSkillRepoMock = $this->getMockBuilder(Players::class)
            ->setMethods(['findOneBy'])
            ->getMock();
        $gameDataSkillRepoMock->method('findOneBy')->willReturn($gameDataSkillsTest);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->will(
            $this->returnCallback(
                function ($entityName) use ($gameDataSkillRepoMock, $playerSkillRepoMock) {
                    if ($entityName === 'App\Entity\GameDataSkills') {
                        return $gameDataSkillRepoMock;
                    }

                    if ($entityName === 'App\Entity\PlayersSkills') {
                        return $playerSkillRepoMock;
                    }

                    return true;
                }
            )
        );

        $playerServiceTest = new PlayerService(
            $objectManager,
            $this->createMock(EquipeService::class),
            $this->createMock(MatchDataService::class)
        );

        $this->assertEquals(0, $playerServiceTest->valeurDunJoueur($joueurTest));
    }

}