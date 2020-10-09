<?php


namespace App\Tests\src\Service\PlayerService;


use App\Entity\GameDataSkills;
use App\Entity\Players;
use App\Entity\PlayersSkills;
use App\Service\EquipeService;
use App\Service\InfosService;
use App\Service\MatchDataService;
use App\Service\PlayerService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class LeJoueurEstFanFavoriteTest extends TestCase
{
    /**
     * @test
     */
    public function le_joueur_est_fan_favorite()
    {
        $gameDataSkillsMock = $this->createMock(GameDataSkills::class);
        $gameDataSkillsMock->method('getName')->willReturn('Fan Favorite');
        $gameDataSkillsMock->method('getSkillId')->willReturn(1);

        $joueurMock = $this->createMock(Players::class);
        $joueurMock->method('getPlayerId')->willReturn(1);

        $playerSkillMock = $this->createMock(PlayersSkills::class);
        $playerSkillMock->method('getFSkill')->willReturn($gameDataSkillsMock);
        $playerSkillMock->method('getFPid')->willReturn($joueurMock);

        $gameDataSkillsRepoMock = $this->getMockBuilder(GameDataSkills::class)
            ->setMethods(['findOneBy'])
            ->getMock();
        $gameDataSkillsRepoMock->method('findOneBy')->willReturn($gameDataSkillsMock);

        $playerSkillsRepoMock = $this->getMockBuilder(PlayersSkills::class)
            ->setMethods(['findOneBy'])
            ->getMock();
        $playerSkillsRepoMock->method('findOneBy')->willReturn($playerSkillMock);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->will(
            $this->returnCallback(
                function ($entityName) use ($gameDataSkillsRepoMock, $playerSkillsRepoMock) {
                    if ($entityName === 'App\Entity\PlayersSkills') {
                        return $playerSkillsRepoMock;
                    }

                    if ($entityName === 'App\Entity\GameDataSkills') {
                        return $gameDataSkillsRepoMock;
                    }

                    return true;
                }
            )
        );

        $playerServiceTest = new PlayerService(
            $objectManager,
            $this->createMock(EquipeService::class),
            $this->createMock(MatchDataService::class),
            $this->createMock(InfosService::class)
        );

        $this->assertTrue($playerServiceTest->leJoueurEstFanFavorite($joueurMock));

    }

    /**
     * @test
     */
    public function le_joueur_est_pas_fan_favorite()
    {
        $joueurMock = $this->createMock(Players::class);
        $joueurMock->method('getPlayerId')->willReturn(1);

       $gameDataSkillsRepoMock = $this->getMockBuilder(GameDataSkills::class)
            ->setMethods(['findOneBy'])
            ->getMock();
        $gameDataSkillsRepoMock->method('findOneBy')->willReturn(false);

        $playerSkillsRepoMock = $this->getMockBuilder(PlayersSkills::class)
            ->setMethods(['findOneBy'])
            ->getMock();
        $playerSkillsRepoMock->method('findOneBy')->willReturn(false);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->will(
            $this->returnCallback(
                function ($entityName) use ($gameDataSkillsRepoMock, $playerSkillsRepoMock) {
                    if ($entityName === 'App\Entity\PlayersSkills') {
                        return $playerSkillsRepoMock;
                    }

                    if ($entityName === 'App\Entity\GameDataSkills') {
                        return $gameDataSkillsRepoMock;
                    }

                    return true;
                }
            )
        );

        $playerServiceTest = new PlayerService(
            $objectManager,
            $this->createMock(EquipeService::class),
            $this->createMock(MatchDataService::class),
            $this->createMock(InfosService::class)
        );

        $this->assertFalse($playerServiceTest->leJoueurEstFanFavorite($joueurMock));
    }
}