<?php

namespace App\Tests\src\Service\PlayerService;


use App\Entity\GameDataPlayersBb2020;
use App\Entity\GameDataSkillsBb2020;
use App\Entity\MatchData;
use App\Entity\Players;
use App\Entity\PlayersSkills;
use App\Enum\RulesetEnum;
use App\Tests\src\TestServiceFactory\PlayerServiceTestFactory;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;

class ajoutCompetenceBb2020Test extends TestCase
{
    /**
     * @test
     */
    public function la_competence_principale_s_ajoute_correctement_Bb2020()
    {
        $gameDataPlayerBb2020Mock = $this->createMock(GameDataPlayersBb2020::class);
        $gameDataPlayerBb2020Mock->method('getPrincipales')->willReturn('G');
        $gameDataPlayerBb2020Mock->method('getSecondaires')->willReturn('S');

        $joueurTest = new Players();
        $joueurTest->setStatus(9);
        $joueurTest->setRuleset(RulesetEnum::BB_2020);
        $joueurTest->setSppDepense(2);
        $joueurTest->setFPosBb2020($gameDataPlayerBb2020Mock);

        $gameDataSkillTest = $this->createMock(GameDataSkillsBb2020::class);
        $gameDataSkillTest->method('getCat')->willReturn('G');

        $playerSkillRepoMock = $this->createMock(ObjectRepository::class);
        $playerSkillRepoMock->method('findBy')->willReturn(
            [
                $this->createMock(PlayersSkills::class)
            ]
        );

        $matchDataMock = $this->createMock(MatchData::class);
        $matchDataMock->method('getMvp')->willReturn(1);
        $matchDataMock->method('getBh')->willReturn(1);
        $matchDataMock->method('getTd')->willReturn(1);
        $matchDataMock->method('getDet')->willReturn(1);

        $matchDataRepoMock = $this->createMock(ObjectRepository::class);
        $matchDataRepoMock->method('findBy')->willReturn([$matchDataMock]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->will(
            $this->returnCallback(
                function ($entityName) use ($playerSkillRepoMock, $matchDataRepoMock) {
                    if ($entityName === PlayersSkills::class) {
                        return $playerSkillRepoMock;
                    }

                    if ($entityName === MatchData::class) {
                        return $matchDataRepoMock;
                    }
                    return true;
                }
            )
        );

        $playerService = (new PlayerServiceTestFactory)->getInstance($objectManager);

        $this->assertEquals('ok',$playerService->ajoutCompetenceBb2020($joueurTest, $gameDataSkillTest, false));
        $this->assertEquals(10, $joueurTest->getSppDepense());
    }

    /**
     * @test
     */
    public function la_competence_principale_tiree_au_hasard_s_ajoute_correctement_Bb2020()
    {
        $gameDataPlayerBb2020Mock = $this->createMock(GameDataPlayersBb2020::class);
        $gameDataPlayerBb2020Mock->method('getPrincipales')->willReturn('G');
        $gameDataPlayerBb2020Mock->method('getSecondaires')->willReturn('S');

        $joueurTest = new Players();
        $joueurTest->setStatus(9);
        $joueurTest->setRuleset(RulesetEnum::BB_2020);
        $joueurTest->setSppDepense(2);
        $joueurTest->setFPosBb2020($gameDataPlayerBb2020Mock);

        $gameDataSkillTest = $this->createMock(GameDataSkillsBb2020::class);
        $gameDataSkillTest->method('getCat')->willReturn('G');

        $playerSkillRepoMock = $this->createMock(ObjectRepository::class);
        $playerSkillRepoMock->method('findBy')->willReturn(
            [
                $this->createMock(PlayersSkills::class)
            ]
        );

        $matchDataMock = $this->createMock(MatchData::class);
        $matchDataMock->method('getMvp')->willReturn(1);
        $matchDataMock->method('getBh')->willReturn(1);
        $matchDataMock->method('getTd')->willReturn(1);

        $matchDataRepoMock = $this->createMock(ObjectRepository::class);
        $matchDataRepoMock->method('findBy')->willReturn([$matchDataMock]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->will(
            $this->returnCallback(
                function ($entityName) use ($playerSkillRepoMock, $matchDataRepoMock) {
                    if ($entityName === PlayersSkills::class) {
                        return $playerSkillRepoMock;
                    }

                    if ($entityName === MatchData::class) {
                        return $matchDataRepoMock;
                    }
                    return true;
                }
            )
        );

        $playerService = (new PlayerServiceTestFactory)->getInstance($objectManager);

        $this->assertEquals('ok',$playerService->ajoutCompetenceBb2020($joueurTest, $gameDataSkillTest, true));
        $this->assertEquals(6, $joueurTest->getSppDepense());
    }

    /**
     * @test
     */
    public function la_competence_secondaire_s_ajoute_correctement_Bb2020()
    {
        $gameDataPlayerBb2020Mock = $this->createMock(GameDataPlayersBb2020::class);
        $gameDataPlayerBb2020Mock->method('getPrincipales')->willReturn('G');
        $gameDataPlayerBb2020Mock->method('getSecondaires')->willReturn('S');

        $joueurTest = new Players();
        $joueurTest->setStatus(9);
        $joueurTest->setRuleset(RulesetEnum::BB_2020);
        $joueurTest->setSppDepense(1);
        $joueurTest->setFPosBb2020($gameDataPlayerBb2020Mock);

        $gameDataSkillTest = $this->createMock(GameDataSkillsBb2020::class);
        $gameDataSkillTest->method('getCat')->willReturn('S');

        $playerSkillRepoMock = $this->createMock(ObjectRepository::class);
        $playerSkillRepoMock->method('findBy')->willReturn(
            [
                $this->createMock(PlayersSkills::class)
            ]
        );

        $matchDataMock = $this->createMock(MatchData::class);
        $matchDataMock->method('getMvp')->willReturn(2);
        $matchDataMock->method('getBh')->willReturn(1);
        $matchDataMock->method('getTd')->willReturn(1);
        $matchDataMock->method('getDet')->willReturn(2);

        $matchDataRepoMock = $this->createMock(ObjectRepository::class);
        $matchDataRepoMock->method('findBy')->willReturn([$matchDataMock]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->will(
            $this->returnCallback(
                function ($entityName) use ($playerSkillRepoMock, $matchDataRepoMock) {
                    if ($entityName === PlayersSkills::class) {
                        return $playerSkillRepoMock;
                    }

                    if ($entityName === MatchData::class) {
                        return $matchDataRepoMock;
                    }
                    return true;
                }
            )
        );

        $playerService = (new PlayerServiceTestFactory)->getInstance($objectManager);

        $this->assertEquals('ok',$playerService->ajoutCompetenceBb2020($joueurTest, $gameDataSkillTest, false));
        $this->assertEquals(15, $joueurTest->getSppDepense());
    }

    /**
     * @test
     */
    public function la_competence_secondaire_tiree_au_hasard_s_ajoute_correctement_Bb2020()
    {
        $gameDataPlayerBb2020Mock = $this->createMock(GameDataPlayersBb2020::class);
        $gameDataPlayerBb2020Mock->method('getPrincipales')->willReturn('G');
        $gameDataPlayerBb2020Mock->method('getSecondaires')->willReturn('S');

        $joueurTest = new Players();
        $joueurTest->setStatus(9);
        $joueurTest->setRuleset(RulesetEnum::BB_2020);
        $joueurTest->setSppDepense(1);
        $joueurTest->setFPosBb2020($gameDataPlayerBb2020Mock);

        $gameDataSkillTest = $this->createMock(GameDataSkillsBb2020::class);
        $gameDataSkillTest->method('getCat')->willReturn('S');

        $playerSkillRepoMock = $this->createMock(ObjectRepository::class);
        $playerSkillRepoMock->method('findBy')->willReturn(
            [
                $this->createMock(PlayersSkills::class)
            ]
        );

        $matchDataMock = $this->createMock(MatchData::class);
        $matchDataMock->method('getMvp')->willReturn(2);
        $matchDataMock->method('getBh')->willReturn(1);
        $matchDataMock->method('getTd')->willReturn(1);

        $matchDataRepoMock = $this->createMock(ObjectRepository::class);
        $matchDataRepoMock->method('findBy')->willReturn([$matchDataMock]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->will(
            $this->returnCallback(
                function ($entityName) use ($playerSkillRepoMock, $matchDataRepoMock) {
                    if ($entityName === PlayersSkills::class) {
                        return $playerSkillRepoMock;
                    }

                    if ($entityName === MatchData::class) {
                        return $matchDataRepoMock;
                    }
                    return true;
                }
            )
        );

        $playerService = (new PlayerServiceTestFactory)->getInstance($objectManager);

        $this->assertEquals('ok',$playerService->ajoutCompetenceBb2020($joueurTest, $gameDataSkillTest, true));
        $this->assertEquals(9, $joueurTest->getSppDepense());
    }

    /**
     * @test
     */
    public function pas_assez_d_xp_pour_une_comp_principale_Bb2020()
    {
        $gameDataPlayerBb2020Mock = $this->createMock(GameDataPlayersBb2020::class);
        $gameDataPlayerBb2020Mock->method('getPrincipales')->willReturn('G');
        $gameDataPlayerBb2020Mock->method('getSecondaires')->willReturn('S');

        $joueurTest = new Players();
        $joueurTest->setStatus(9);
        $joueurTest->setRuleset(RulesetEnum::BB_2020);
        $joueurTest->setSppDepense(7);
        $joueurTest->setFPosBb2020($gameDataPlayerBb2020Mock);

        $gameDataSkillTest = $this->createMock(GameDataSkillsBb2020::class);
        $gameDataSkillTest->method('getCat')->willReturn('G');

        $playerSkillRepoMock = $this->createMock(ObjectRepository::class);
        $playerSkillRepoMock->method('findBy')->willReturn(
            [
                $this->createMock(PlayersSkills::class)
            ]
        );

        $matchDataMock = $this->createMock(MatchData::class);
        $matchDataMock->method('getMvp')->willReturn(1);
        $matchDataMock->method('getBh')->willReturn(1);
        $matchDataMock->method('getTd')->willReturn(1);

        $matchDataRepoMock = $this->createMock(ObjectRepository::class);
        $matchDataRepoMock->method('findBy')->willReturn([$matchDataMock]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->will(
            $this->returnCallback(
                function ($entityName) use ($playerSkillRepoMock, $matchDataRepoMock) {
                    if ($entityName === PlayersSkills::class) {
                        return $playerSkillRepoMock;
                    }

                    if ($entityName === MatchData::class) {
                        return $matchDataRepoMock;
                    }
                    return true;
                }
            )
        );

        $playerService = (new PlayerServiceTestFactory)->getInstance($objectManager);

        $this->assertEquals('Pas assez de points d\'XP',$playerService->ajoutCompetenceBb2020($joueurTest, $gameDataSkillTest, false));
        $this->assertEquals(7, $joueurTest->getSppDepense());
    }

    /**
     * @test
     */
    public function pas_assez_d_xp_pour_une_comp_secondaire_Bb2020()
    {
        $gameDataPlayerBb2020Mock = $this->createMock(GameDataPlayersBb2020::class);
        $gameDataPlayerBb2020Mock->method('getPrincipales')->willReturn('G');
        $gameDataPlayerBb2020Mock->method('getSecondaires')->willReturn('S');

        $joueurTest = new Players();
        $joueurTest->setStatus(9);
        $joueurTest->setRuleset(RulesetEnum::BB_2020);
        $joueurTest->setSppDepense(5);
        $joueurTest->setFPosBb2020($gameDataPlayerBb2020Mock);

        $gameDataSkillTest = $this->createMock(GameDataSkillsBb2020::class);
        $gameDataSkillTest->method('getCat')->willReturn('S');

        $playerSkillRepoMock = $this->createMock(ObjectRepository::class);
        $playerSkillRepoMock->method('findBy')->willReturn(
            [
                $this->createMock(PlayersSkills::class)
            ]
        );

        $matchDataMock = $this->createMock(MatchData::class);
        $matchDataMock->method('getMvp')->willReturn(1);
        $matchDataMock->method('getBh')->willReturn(1);
        $matchDataMock->method('getTd')->willReturn(1);

        $matchDataRepoMock = $this->createMock(ObjectRepository::class);
        $matchDataRepoMock->method('findBy')->willReturn([$matchDataMock]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->will(
            $this->returnCallback(
                function ($entityName) use ($playerSkillRepoMock, $matchDataRepoMock) {
                    if ($entityName === PlayersSkills::class) {
                        return $playerSkillRepoMock;
                    }

                    if ($entityName === MatchData::class) {
                        return $matchDataRepoMock;
                    }
                    return true;
                }
            )
        );

        $playerService = (new PlayerServiceTestFactory)->getInstance($objectManager);

        $this->assertEquals('Pas assez de points d\'XP',$playerService->ajoutCompetenceBb2020($joueurTest, $gameDataSkillTest, false));
        $this->assertEquals(5, $joueurTest->getSppDepense());
    }

    /**
     * @test
     */
    public function plus_de_six_comp_bb2020()
    {
        $gameDataPlayerBb2020Mock = $this->createMock(GameDataPlayersBb2020::class);
        $gameDataPlayerBb2020Mock->method('getPrincipales')->willReturn('G');
        $gameDataPlayerBb2020Mock->method('getSecondaires')->willReturn('S');

        $joueurTest = new Players();
        $joueurTest->setStatus(9);
        $joueurTest->setRuleset(RulesetEnum::BB_2020);
        $joueurTest->setSppDepense(5);
        $joueurTest->setFPosBb2020($gameDataPlayerBb2020Mock);

        $gameDataSkillTest = $this->createMock(GameDataSkillsBb2020::class);
        $gameDataSkillTest->method('getCat')->willReturn('S');

        $playerSkillRepoMock = $this->createMock(ObjectRepository::class);
        $playerSkillRepoMock->method('findBy')->willReturn(
            [
                $this->createMock(PlayersSkills::class),
                $this->createMock(PlayersSkills::class),
                $this->createMock(PlayersSkills::class),
                $this->createMock(PlayersSkills::class),
                $this->createMock(PlayersSkills::class),
                $this->createMock(PlayersSkills::class)
            ]
        );

        $matchDataMock = $this->createMock(MatchData::class);
        $matchDataMock->method('getMvp')->willReturn(1);
        $matchDataMock->method('getBh')->willReturn(1);
        $matchDataMock->method('getTd')->willReturn(1);

        $matchDataRepoMock = $this->createMock(ObjectRepository::class);
        $matchDataRepoMock->method('findBy')->willReturn([$matchDataMock]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->will(
            $this->returnCallback(
                function ($entityName) use ($playerSkillRepoMock, $matchDataRepoMock) {
                    if ($entityName === PlayersSkills::class) {
                        return $playerSkillRepoMock;
                    }

                    if ($entityName === MatchData::class) {
                        return $matchDataRepoMock;
                    }
                    return true;
                }
            )
        );

        $playerService = (new PlayerServiceTestFactory)->getInstance($objectManager);

        $this->assertEquals('Niveau Max Atteint',$playerService->ajoutCompetenceBb2020($joueurTest, $gameDataSkillTest, false));
        $this->assertEquals(5, $joueurTest->getSppDepense());
    }

    /**
     * @test
     */
    public function une_stat_est_bien_augmentee_bb2020()
    {
        $gameDataPlayerBb2020Mock = $this->createMock(GameDataPlayersBb2020::class);
        $gameDataPlayerBb2020Mock->method('getPrincipales')->willReturn('G');
        $gameDataPlayerBb2020Mock->method('getSecondaires')->willReturn('S');

        $joueurTest = new Players();
        $joueurTest->setStatus(9);
        $joueurTest->setRuleset(RulesetEnum::BB_2020);
        $joueurTest->setSppDepense(5);
        $joueurTest->setFPosBb2020($gameDataPlayerBb2020Mock);

        $gameDataSkillTest = $this->createMock(GameDataSkillsBb2020::class);
        $gameDataSkillTest->method('getCat')->willReturn('C');
        $gameDataSkillTest->method('getId')->willReturn(80);

        $playerSkillRepoMock = $this->createMock(ObjectRepository::class);
        $playerSkillRepoMock->method('findBy')->willReturn(
            [
                $this->createMock(PlayersSkills::class)
            ]
        );

        $matchDataMock = $this->createMock(MatchData::class);
        $matchDataMock->method('getMvp')->willReturn(4);
        $matchDataMock->method('getBh')->willReturn(3);
        $matchDataMock->method('getTd')->willReturn(1);

        $matchDataRepoMock = $this->createMock(ObjectRepository::class);
        $matchDataRepoMock->method('findBy')->willReturn([$matchDataMock]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->will(
            $this->returnCallback(
                function ($entityName) use ($playerSkillRepoMock, $matchDataRepoMock) {
                    if ($entityName === PlayersSkills::class) {
                        return $playerSkillRepoMock;
                    }

                    if ($entityName === MatchData::class) {
                        return $matchDataRepoMock;
                    }
                    return true;
                }
            )
        );

        $playerService = (new PlayerServiceTestFactory)->getInstance($objectManager);

        $this->assertEquals('ok',$playerService->ajoutCompetenceBb2020($joueurTest, $gameDataSkillTest, false));
        $this->assertEquals(25, $joueurTest->getSppDepense());
    }
}