<?php

namespace App\Tests\src\Service\PlayerService;


use App\Entity\GameDataPlayers;
use App\Entity\GameDataPlayersBb2020;
use App\Entity\GameDataSkills;
use App\Entity\GameDataSkillsBb2020;
use App\Entity\Players;
use App\Entity\PlayersSkills;
use App\Enum\RulesetEnum;
use App\Tests\src\TestServiceFactory\PlayerServiceTestFactory;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;

class valeurDunJoueurTest extends TestCase
{
    /**
     * @test
     */
    public function la_valeur_de_base_est_bien_retournee_Bb2016(): void
    {
        $positionTest = new GameDataPlayers();
        $positionTest->setCost(50_000);

        $joueurTest = new Players();
        $joueurTest->setFPos($positionTest);
        $joueurTest->setRuleset(RulesetEnum::BB_2016);

        $gameDataSkillFanFavorite = $this->createMock(GameDataSkills::class);
        $gameDataSkillFanFavorite->method('getSkillId')->willReturn(1);

        $gameDataSkillRepoMock = $this->createMock(ObjectRepository::class);
        $gameDataSkillRepoMock->method('findOneBy')->willReturn($gameDataSkillFanFavorite);

        $playerSkillRepoMock = $this->createMock(ObjectRepository::class);
        $playerSkillRepoMock->method('findBy')->willReturn([]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->will(
            $this->returnCallback(
                function ($entityName) use ($playerSkillRepoMock,$gameDataSkillRepoMock) {
                    if ($entityName === PlayersSkills::class) {
                        return $playerSkillRepoMock;
                    }

                    if ($entityName === GameDataSkills::class) {
                        return $gameDataSkillRepoMock;
                    }
                    return true;
                }
            )
        );

        $playerServiceTest = (new PlayerServiceTestFactory())->getInstance(
            $objectManager
        );

        $this->assertEquals(50_000, $playerServiceTest->valeurDunJoueur($joueurTest));
    }

    /**
     * @test
     */
    public function la_valeur_de_base_est_bien_retournee_Bb2020(): void
    {
        $positionTest = new GameDataPlayersBb2020();
        $positionTest->setCost(50_000);

        $joueurTest = new Players();
        $joueurTest->setFPosBb2020($positionTest);
        $joueurTest->setRuleset(RulesetEnum::BB_2020);

        $gameDataSkillFanFavorite = $this->createMock(GameDataSkillsBb2020::class);
        $gameDataSkillFanFavorite->method('getId')->willReturn(1);

        $gameDataSkillRepoMock = $this->createMock(ObjectRepository::class);
        $gameDataSkillRepoMock->method('findOneBy')->willReturn($gameDataSkillFanFavorite);

        $playerSkillRepoMock = $this->createMock(ObjectRepository::class);
        $playerSkillRepoMock->method('findBy')->willReturn([]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->will(
            $this->returnCallback(
                function ($entityName) use ($playerSkillRepoMock,$gameDataSkillRepoMock) {
                    if ($entityName === PlayersSkills::class) {
                        return $playerSkillRepoMock;
                    }

                    if ($entityName === GameDataSkillsBb2020::class) {
                        return $gameDataSkillRepoMock;
                    }
                    return true;
                }
            )
        );

        $playerServiceTest = (new PlayerServiceTestFactory())->getInstance(
            $objectManager
        );

        $this->assertEquals(50_000, $playerServiceTest->valeurDunJoueur($joueurTest));
    }

    /**
     * @test
     */
    public function les_comps_simples_sont_bien_comptees_bb2016(): void
    {
        $positionTest = new GameDataPlayers();
        $positionTest->setCost(50_000);

        $gameDataSkillTest = new GameDataSkills();

        $playerSkillTest = new PlayersSkills();
        $playerSkillTest->setType('N');
        $playerSkillTest->setFSkill($gameDataSkillTest);

        $joueurTest = new Players();
        $joueurTest->setFPos($positionTest);
        $joueurTest->addSkills($playerSkillTest);
        $joueurTest->setRuleset(RulesetEnum::BB_2016);

        $playerSkillRepoMock = $this->createMock(ObjectRepository::class);
        $playerSkillRepoMock->method('findBy')->willReturn([$playerSkillTest]);

        $gameDataSkillFanFavorite = $this->createMock(GameDataSkills::class);
        $gameDataSkillFanFavorite->method('getSkillId')->willReturn(1);

        $gameDataSkillRepoMock = $this->createMock(ObjectRepository::class);
        $gameDataSkillRepoMock->method('findOneBy')->willReturn($gameDataSkillFanFavorite);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->will(
            $this->returnCallback(
                function ($entityName) use ($playerSkillRepoMock,$gameDataSkillRepoMock) {
                    if ($entityName === PlayersSkills::class) {
                        return $playerSkillRepoMock;
                    }

                    if ($entityName === GameDataSkills::class) {
                        return $gameDataSkillRepoMock;
                    }
                    return true;
                }
            )
        );

        $playerServiceTest = (new PlayerServiceTestFactory())->getInstance(
            $objectManager
        );

        $this->assertEquals(70_000, $playerServiceTest->valeurDunJoueur($joueurTest));
    }

    /**
     * @test
     */
    public function les_comps_principales_sont_bien_comptees_bb2020(): void
    {
        $positionTest = new GameDataPlayersBb2020();
        $positionTest->setCost(50_000);

        $gameDataSkillTest = new GameDataSkillsBb2020();

        $playerSkillTest = new PlayersSkills();
        $playerSkillTest->setType('P');
        $playerSkillTest->setFSkillBb2020($gameDataSkillTest);

        $joueurTest = new Players();
        $joueurTest->setFPosBb2020($positionTest);
        $joueurTest->addSkills($playerSkillTest);
        $joueurTest->setRuleset(RulesetEnum::BB_2020);

        $playerSkillRepoMock = $this->createMock(ObjectRepository::class);
        $playerSkillRepoMock->method('findBy')->willReturn([$playerSkillTest]);

        $gameDataSkillFanFavorite = $this->createMock(GameDataSkillsBb2020::class);
        $gameDataSkillFanFavorite->method('getId')->willReturn(1);

        $gameDataSkillRepoMock = $this->createMock(ObjectRepository::class);
        $gameDataSkillRepoMock->method('findOneBy')->willReturn($gameDataSkillFanFavorite);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->will(
            $this->returnCallback(
                function ($entityName) use ($playerSkillRepoMock,$gameDataSkillRepoMock) {
                    if ($entityName === PlayersSkills::class) {
                        return $playerSkillRepoMock;
                    }

                    if ($entityName === GameDataSkillsBb2020::class) {
                        return $gameDataSkillRepoMock;
                    }
                    return true;
                }
            )
        );

        $playerServiceTest = (new PlayerServiceTestFactory())->getInstance(
            $objectManager
        );

        $this->assertEquals(70_000, $playerServiceTest->valeurDunJoueur($joueurTest));
    }

    /**
     * @test
     */
    public function les_comps_doubles_sont_bien_comptees_bb2016(): void
    {
        $positionTest = new GameDataPlayers();
        $positionTest->setCost(50_000);

        $gameDataSkillTest = new GameDataSkills();

        $playerSkillTest = new PlayersSkills();
        $playerSkillTest->setType('D');
        $playerSkillTest->setFSkill($gameDataSkillTest);

        $joueurTest = new Players();
        $joueurTest->setFPos($positionTest);
        $joueurTest->addSkills($playerSkillTest);
        $joueurTest->setRuleset(RulesetEnum::BB_2016);

        $playerSkillRepoMock = $this->createMock(ObjectRepository::class);
        $playerSkillRepoMock->method('findBy')->willReturn([$playerSkillTest]);

        $gameDataSkillFanFavorite = $this->createMock(GameDataSkills::class);
        $gameDataSkillFanFavorite->method('getSkillId')->willReturn(1);

        $gameDataSkillRepoMock = $this->createMock(ObjectRepository::class);
        $gameDataSkillRepoMock->method('findOneBy')->willReturn($gameDataSkillFanFavorite);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->will(
            $this->returnCallback(
                function ($entityName) use ($playerSkillRepoMock,$gameDataSkillRepoMock) {
                    if ($entityName === PlayersSkills::class) {
                        return $playerSkillRepoMock;
                    }

                    if ($entityName === GameDataSkills::class) {
                        return $gameDataSkillRepoMock;
                    }
                    return true;
                }
            )
        );

        $playerServiceTest = (new PlayerServiceTestFactory())->getInstance(
            $objectManager
        );

        $this->assertEquals(80_000, $playerServiceTest->valeurDunJoueur($joueurTest));
    }

    /**
     * @test
     */
    public function les_comps_secondaire_sont_bien_comptees_bb2020(): void
    {
        $positionTest = new GameDataPlayersBb2020();
        $positionTest->setCost(50_000);

        $gameDataSkillTest = new GameDataSkillsBb2020();

        $playerSkillTest = new PlayersSkills();
        $playerSkillTest->setType('S');
        $playerSkillTest->setFSkillBb2020($gameDataSkillTest);

        $joueurTest = new Players();
        $joueurTest->setFPosBb2020($positionTest);
        $joueurTest->addSkills($playerSkillTest);
        $joueurTest->setRuleset(RulesetEnum::BB_2020);

        $playerSkillRepoMock = $this->createMock(ObjectRepository::class);
        $playerSkillRepoMock->method('findBy')->willReturn([$playerSkillTest]);

        $gameDataSkillFanFavorite = $this->createMock(GameDataSkillsBb2020::class);
        $gameDataSkillFanFavorite->method('getId')->willReturn(1);

        $gameDataSkillRepoMock = $this->createMock(ObjectRepository::class);
        $gameDataSkillRepoMock->method('findOneBy')->willReturn($gameDataSkillFanFavorite);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->will(
            $this->returnCallback(
                function ($entityName) use ($playerSkillRepoMock,$gameDataSkillRepoMock) {
                    if ($entityName === PlayersSkills::class) {
                        return $playerSkillRepoMock;
                    }

                    if ($entityName === GameDataSkillsBb2020::class) {
                        return $gameDataSkillRepoMock;
                    }
                    return true;
                }
            )
        );

        $playerServiceTest = (new PlayerServiceTestFactory())->getInstance(
            $objectManager
        );

        $this->assertEquals(90_000, $playerServiceTest->valeurDunJoueur($joueurTest));
    }


    /**
     * @test
     */
    public function les_augmentations_de_stats_sont_bien_comptees_bb2016(): void
    {
        $positionTest = new GameDataPlayers();
        $positionTest->setCost(50_000);

        $joueurTest = new Players();
        $joueurTest->setFPos($positionTest);
        $joueurTest->setAchMa(1);
        $joueurTest->setAchAg(1);
        $joueurTest->setAchSt(1);
        $joueurTest->setAchAv(1);
        $joueurTest->setRuleset(RulesetEnum::BB_2016);

        $playerSkillRepoMock = $this->createMock(ObjectRepository::class);
        $playerSkillRepoMock->method('findBy')->willReturn([]);

        $gameDataSkillFanFavorite = $this->createMock(GameDataSkills::class);
        $gameDataSkillFanFavorite->method('getSkillId')->willReturn(1);

        $gameDataSkillRepoMock = $this->createMock(ObjectRepository::class);
        $gameDataSkillRepoMock->method('findOneBy')->willReturn($gameDataSkillFanFavorite);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->will(
            $this->returnCallback(
                function ($entityName) use ($playerSkillRepoMock,$gameDataSkillRepoMock) {
                    if ($entityName === PlayersSkills::class) {
                        return $playerSkillRepoMock;
                    }

                    if ($entityName === GameDataSkills::class) {
                        return $gameDataSkillRepoMock;
                    }
                    return true;
                }
            )
        );

        $playerServiceTest = (new PlayerServiceTestFactory())->getInstance(
            $objectManager
        );

        $this->assertEquals(200_000, $playerServiceTest->valeurDunJoueur($joueurTest));
    }

    /**
     * @test
     */
    public function le_joueur_a_plusieurs_type_de_comp_bb2016(): void
    {
        $positionTest = new GameDataPlayers();
        $positionTest->setCost(50_000);

        $gameDataSkillTest = new GameDataSkills();
        $gameDataSkillTest->setName('test skill');

        $playerSkillTest0 = new PlayersSkills();
        $playerSkillTest0->setType('N');
        $playerSkillTest0->setFSkill($gameDataSkillTest);

        $playerSkillTest1 = new PlayersSkills();
        $playerSkillTest1->setType('D');
        $playerSkillTest1->setFSkill($gameDataSkillTest);

        $joueurTest = new Players();
        $joueurTest->setFPos($positionTest);
        $joueurTest->addSkills($playerSkillTest0);
        $joueurTest->addSkills($playerSkillTest1);
        $joueurTest->setRuleset(RulesetEnum::BB_2016);

        $playerSkillRepoMock = $this->createMock(ObjectRepository::class);
        $playerSkillRepoMock->method('findBy')->willReturn([$playerSkillTest0,$playerSkillTest1]);

        $gameDataSkillFanFavorite = $this->createMock(GameDataSkills::class);
        $gameDataSkillFanFavorite->method('getSkillId')->willReturn(1);

        $gameDataSkillRepoMock = $this->createMock(ObjectRepository::class);
        $gameDataSkillRepoMock->method('findOneBy')->willReturn($gameDataSkillFanFavorite);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->will(
            $this->returnCallback(
                function ($entityName) use ($playerSkillRepoMock,$gameDataSkillRepoMock) {
                    if ($entityName === PlayersSkills::class) {
                        return $playerSkillRepoMock;
                    }

                    if ($entityName === GameDataSkills::class) {
                        return $gameDataSkillRepoMock;
                    }
                    return true;
                }
            )
        );

        $playerServiceTest = (new PlayerServiceTestFactory())->getInstance(
            $objectManager
        );

        $this->assertEquals(100_000, $playerServiceTest->valeurDunJoueur($joueurTest));
    }
    /**
     * @test
     */
    public function le_joueur_a_plusieurs_type_de_comp_bb2020(): void
    {
        $positionTest = new GameDataPlayersBb2020();
        $positionTest->setCost(50_000);

        $gameDataSkillTest = new GameDataSkillsBb2020();
        $gameDataSkillTest->setName('test skill');

        $playerSkillTest0 = new PlayersSkills();
        $playerSkillTest0->setType('P');
        $playerSkillTest0->setFSkillBb2020($gameDataSkillTest);

        $playerSkillTest1 = new PlayersSkills();
        $playerSkillTest1->setType('S');
        $playerSkillTest1->setFSkillBb2020($gameDataSkillTest);

        $joueurTest = new Players();
        $joueurTest->setFPosBb2020($positionTest);
        $joueurTest->addSkills($playerSkillTest0);
        $joueurTest->addSkills($playerSkillTest1);
        $joueurTest->setRuleset(RulesetEnum::BB_2020);

        $playerSkillRepoMock = $this->createMock(ObjectRepository::class);
        $playerSkillRepoMock->method('findBy')->willReturn([$playerSkillTest0,$playerSkillTest1]);

        $gameDataSkillFanFavorite = $this->createMock(GameDataSkillsBb2020::class);
        $gameDataSkillFanFavorite->method('getId')->willReturn(1);

        $gameDataSkillRepoMock = $this->createMock(ObjectRepository::class);
        $gameDataSkillRepoMock->method('findOneBy')->willReturn($gameDataSkillFanFavorite);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->will(
            $this->returnCallback(
                function ($entityName) use ($playerSkillRepoMock,$gameDataSkillRepoMock) {
                    if ($entityName === PlayersSkills::class) {
                        return $playerSkillRepoMock;
                    }

                    if ($entityName === GameDataSkillsBb2020::class) {
                        return $gameDataSkillRepoMock;
                    }
                    return true;
                }
            )
        );

        $playerServiceTest = (new PlayerServiceTestFactory())->getInstance(
            $objectManager
        );

        $this->assertEquals(110_000, $playerServiceTest->valeurDunJoueur($joueurTest));
    }

    /**
     * @test
     */
    public function le_joueur_a_disposable_bb2016()
    {
        $gameDataSkillsTest = $this->createMock(GameDataSkills::class);
        $gameDataSkillsTest->method('getName')->willReturn('Disposable');
        $gameDataSkillsTest->method('getSkillId')->willReturn(1);

        $positionTest = new GameDataPlayers();
        $positionTest->setCost(50_000);
        $positionTest->addBaseSkill($gameDataSkillsTest);

        $joueurTest = new Players();
        $joueurTest->setFPos($positionTest);
        $joueurTest->setRuleset(RulesetEnum::BB_2016);

        $playerSkillRepoMock = $this->createMock(ObjectRepository::class);
        $playerSkillRepoMock->method('findBy')->willReturn([]);

        $gameDataSkillRepoMock = $this->getMockBuilder(Players::class)
            ->addMethods(['findOneBy'])
            ->getMock();
        $gameDataSkillRepoMock->method('findOneBy')->willReturn($gameDataSkillsTest);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->will(
            $this->returnCallback(
                function ($entityName) use ($gameDataSkillRepoMock, $playerSkillRepoMock) {
                    if ($entityName === GameDataSkills::class) {
                        return $gameDataSkillRepoMock;
                    }

                    if ($entityName === PlayersSkills::class) {
                        return $playerSkillRepoMock;
                    }

                    return true;
                }
            )
        );

        $playerServiceTest = (new PlayerServiceTestFactory())->getInstance(
            $objectManager
        );

        $this->assertEquals(0, $playerServiceTest->valeurDunJoueur($joueurTest));
    }

    /**
     * @test
     */
    public function le_joueur_a_disposable_bb2020()
    {
        $gameDataSkillsTest = $this->createMock(GameDataSkillsBb2020::class);
        $gameDataSkillsTest->method('getName')->willReturn('Disposable');
        $gameDataSkillsTest->method('getId')->willReturn(1);

        $positionTest = new GameDataPlayersBb2020();
        $positionTest->setCost(50_000);
        $positionTest->addBaseSkill($gameDataSkillsTest);

        $joueurTest = new Players();
        $joueurTest->setFPosBb2020($positionTest);
        $joueurTest->setRuleset(RulesetEnum::BB_2020);

        $playerSkillRepoMock = $this->createMock(ObjectRepository::class);
        $playerSkillRepoMock->method('findBy')->willReturn([]);

        $gameDataSkillRepoMock = $this->getMockBuilder(Players::class)
            ->addMethods(['findOneBy'])
            ->getMock();
        $gameDataSkillRepoMock->method('findOneBy')->willReturn($gameDataSkillsTest);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->will(
            $this->returnCallback(
                function ($entityName) use ($gameDataSkillRepoMock, $playerSkillRepoMock) {
                    if ($entityName === GameDataSkillsBb2020::class) {
                        return $gameDataSkillRepoMock;
                    }

                    if ($entityName === PlayersSkills::class) {
                        return $playerSkillRepoMock;
                    }

                    return true;
                }
            )
        );

        $playerServiceTest = (new PlayerServiceTestFactory())->getInstance(
            $objectManager
        );

        $this->assertEquals(0, $playerServiceTest->valeurDunJoueur($joueurTest));
    }
}