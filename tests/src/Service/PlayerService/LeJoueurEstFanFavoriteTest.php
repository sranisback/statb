<?php


namespace App\Tests\src\Service\PlayerService;


use App\Entity\GameDataSkills;
use App\Entity\GameDataSkillsBb2020;
use App\Entity\Players;
use App\Entity\PlayersSkills;
use App\Enum\RulesetEnum;
use App\Tests\src\TestServiceFactory\PlayerServiceTestFactory;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class LeJoueurEstFanFavoriteTest extends TestCase
{
    /**
     * @test
     */
    public function le_joueur_est_fan_favorite_bb2016()
    {
        $gameDataSkillsMock = $this->createMock(GameDataSkills::class);
        $gameDataSkillsMock->method('getName')->willReturn('Fan Favorite');
        $gameDataSkillsMock->method('getSkillId')->willReturn(1);

        $joueurMock = $this->createMock(Players::class);

        $playerSkillMock = $this->createMock(PlayersSkills::class);
        $playerSkillMock->method('getFSkill')->willReturn($gameDataSkillsMock);
        $playerSkillMock->method('getFPid')->willReturn($joueurMock);

        $joueurMock->method('getPlayerId')->willReturn(1);
        $joueurMock->method('getRuleset')->willReturn(RulesetEnum::BB_2016);
        $joueurMock->method('getSkills')->willReturn($playerSkillMock);

        $gameDataSkillsRepoMock = $this->getMockBuilder(GameDataSkills::class)
            ->addMethods(['findOneBy'])
            ->getMock();
        $gameDataSkillsRepoMock->method('findOneBy')->willReturn($gameDataSkillsMock);

        $playerSkillsRepoMock = $this->getMockBuilder(PlayersSkills::class)
            ->addMethods(['findOneBy'])
            ->getMock();
        $playerSkillsRepoMock->method('findOneBy')->willReturn($playerSkillMock);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->will(
            $this->returnCallback(
                function ($entityName) use ($gameDataSkillsRepoMock, $playerSkillsRepoMock) {
                    if ($entityName === PlayersSkills::class) {
                        return $playerSkillsRepoMock;
                    }

                    if ($entityName === GameDataSkills::class) {
                        return $gameDataSkillsRepoMock;
                    }

                    return true;
                }
            )
        );

        $playerServiceTest =(new PlayerServiceTestFactory)->getInstance(
            $objectManager
        );

        $this->assertTrue($playerServiceTest->leJoueurEstFanFavorite($joueurMock));
    }

    /**
     * @test
     */
    public function le_joueur_est_pas_fan_favorite_bb2016()
    {
        $gameDataSkillsMock = $this->createMock(GameDataSkills::class);
        $gameDataSkillsMock->method('getName')->willReturn('Fan Favorite');
        $gameDataSkillsMock->method('getSkillId')->willReturn(1);

        $joueurMock = $this->createMock(Players::class);
        $joueurMock->method('getPlayerId')->willReturn(1);
        $joueurMock->method('getRuleset')->willReturn(RulesetEnum::BB_2016);

       $gameDataSkillsRepoMock = $this->getMockBuilder(GameDataSkills::class)
            ->addMethods(['findOneBy'])
            ->getMock();
        $gameDataSkillsRepoMock->method('findOneBy')->willReturn($gameDataSkillsMock);

        $playerSkillRepoMock = $this->getMockBuilder(PlayersSkills::class)
            ->addMethods(['findOneBy'])
            ->getMock();
        $playerSkillRepoMock->method('findOneBy')->willReturn(null);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->will(
            $this->returnCallback(
                function ($entityName) use ($gameDataSkillsRepoMock, $playerSkillRepoMock) {
                    if ($entityName === PlayersSkills::class) {
                        return $playerSkillRepoMock;
                    }

                    if ($entityName === GameDataSkills::class) {
                        return $gameDataSkillsRepoMock;
                    }

                    return true;
                }
            )
        );

        $playerServiceTest = (new PlayerServiceTestFactory)->getInstance(
            $objectManager
        );

        $this->assertFalse($playerServiceTest->leJoueurEstFanFavorite($joueurMock));
    }

    /**
     * @test
     */
    public function le_joueur_est_fan_favorite_bb2020()
    {
        $gameDataSkillsMock = $this->createMock(GameDataSkillsBb2020::class);
        $gameDataSkillsMock->method('getName')->willReturn('Fan Favorite');
        $gameDataSkillsMock->method('getId')->willReturn(1);

        $joueurMock = $this->createMock(Players::class);
        $joueurMock->method('getPlayerId')->willReturn(1);
        $joueurMock->method('getRuleset')->willReturn(RulesetEnum::BB_2020);

        $playerSkillMock = $this->createMock(PlayersSkills::class);
        $playerSkillMock->method('getFSkillBb2020')->willReturn($gameDataSkillsMock);
        $playerSkillMock->method('getFPid')->willReturn($joueurMock);

        $gameDataSkillsRepoMock = $this->getMockBuilder(GameDataSkillsBb2020::class)
            ->addMethods(['findOneBy'])
            ->getMock();
        $gameDataSkillsRepoMock->method('findOneBy')->willReturn($gameDataSkillsMock);

        $playerSkillsRepoMock = $this->getMockBuilder(PlayersSkills::class)
            ->addMethods(['findOneBy'])
            ->getMock();
        $playerSkillsRepoMock->method('findOneBy')->willReturn($playerSkillMock);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->will(
            $this->returnCallback(
                function ($entityName) use ($gameDataSkillsRepoMock, $playerSkillsRepoMock) {
                    if ($entityName === PlayersSkills::class) {
                        return $playerSkillsRepoMock;
                    }

                    if ($entityName === GameDataSkillsBb2020::class) {
                        return $gameDataSkillsRepoMock;
                    }

                    return true;
                }
            )
        );

        $playerServiceTest = (new PlayerServiceTestFactory)->getInstance(
            $objectManager
        );

        $this->assertTrue($playerServiceTest->leJoueurEstFanFavorite($joueurMock));
    }

    /**
     * @test
     */
    public function le_joueur_est_pas_fan_favorite_bb2020()
    {
        $gameDataSkillsMock = $this->createMock(GameDataSkillsBb2020::class);
        $gameDataSkillsMock->method('getName')->willReturn('Fan Favorite');
        $gameDataSkillsMock->method('getId')->willReturn(1);

        $joueurMock = $this->createMock(Players::class);
        $joueurMock->method('getPlayerId')->willReturn(1);
        $joueurMock->method('getRuleset')->willReturn(RulesetEnum::BB_2020);

        $gameDataSkillsRepoMock = $this->getMockBuilder(GameDataSkillsBb2020::class)
            ->addMethods(['findOneBy'])
            ->getMock();
        $gameDataSkillsRepoMock->method('findOneBy')->willReturn($gameDataSkillsMock);

        $playerSkillRepoMock = $this->getMockBuilder(PlayersSkills::class)
            ->addMethods(['findOneBy'])
            ->getMock();
        $playerSkillRepoMock->method('findOneBy')->willReturn(null);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->will(
            $this->returnCallback(
                function ($entityName) use ($gameDataSkillsRepoMock, $playerSkillRepoMock) {
                    if ($entityName === PlayersSkills::class) {
                        return $playerSkillRepoMock;
                    }

                    if ($entityName === GameDataSkillsBb2020::class) {
                        return $gameDataSkillsRepoMock;
                    }

                    return true;
                }
            )
        );

        $playerServiceTest = (new PlayerServiceTestFactory)->getInstance(
            $objectManager
        );

        $this->assertFalse($playerServiceTest->leJoueurEstFanFavorite($joueurMock));
    }
}