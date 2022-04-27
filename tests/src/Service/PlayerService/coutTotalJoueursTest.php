<?php


namespace App\Tests\src\Service\PlayerService;

use App\Entity\GameDataPlayers;
use App\Entity\GameDataPlayersBb2020;
use App\Entity\GameDataSkills;
use App\Entity\GameDataSkillsBb2020;
use App\Entity\Players;
use App\Entity\PlayersSkills;
use App\Entity\Teams;
use App\Enum\RulesetEnum;
use App\Service\EquipeGestionService;
use App\Service\EquipeService;
use App\Service\InfosService;
use App\Service\MatchDataService;
use App\Service\PlayerService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class coutTotalJoueursTest extends TestCase
{
    /**
     * @test
     */
    public function le_cout_total_des_joueurs_est_bien_calcule_bb2016(): void
    {
        $equipeMock = $this->createMock(Teams::class);

        $baseSkillsTest = new ArrayCollection();

        $positionMock = $this->createMock(GameDataPlayers::class);
        $positionMock->method('getCost')->willReturn(110_000);
        $positionMock->method('getBaseSkills')->willReturn($baseSkillsTest);

        $gameDataSkillMock = $this->createMock(GameDataSkills::class);

        $gameDataSkillsMockDisposable = $this->createMock(GameDataSkills::class);

        $gameDataSkillRepoMock = $this->getMockBuilder(Players::class)
            ->addMethods(['findOneBy'])
            ->getMock();
        $gameDataSkillRepoMock->method('findOneBy')->willReturn($gameDataSkillsMockDisposable);

        $playersSkillMock = $this->createMock(PlayersSkills::class);
        $playersSkillMock->method('getType')->willReturn('N');
        $playersSkillMock->method('getFSkill')->willReturn($gameDataSkillMock);

        $playerSkillsTest = new ArrayCollection();
        $playerSkillsTest->add($playersSkillMock);

        $joueurMock = $this->createMock(Players::class);
        $joueurMock->method('getFPos')->willReturn($positionMock);
        $joueurMock->method('getInjRpm')->willReturn(0);
        $joueurMock->method('getSkills')->willReturn($playerSkillsTest);
        $joueurMock->method('getRuleset')->willReturn(RulesetEnum::BB_2016);

        $playerRepoMock = $this->getMockBuilder(Players::class)
            ->addMethods(['listeDesJoueursPourlEquipe'])
            ->getMock();
        $playerRepoMock->method('listeDesJoueursPourlEquipe')->willReturn(
            [$joueurMock]
        );

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->will($this->returnCallback(
            function ($entityName) use ($playerRepoMock,$gameDataSkillRepoMock) {
                if ($entityName === 'App\Entity\Players') {
                    return $playerRepoMock;
                }

                if ($entityName === 'App\Entity\GameDataSkills') {
                    return $gameDataSkillRepoMock;
                }

                if ($entityName === 'App\Entity\PlayersSkills') {
                    return $this->createMock(ObjectRepository::class);
                }

                return true;
            }
        ));

        $playerServiceTest = new PlayerService(
            $objectManager,
            $this->createMock(EquipeGestionService::class),
            $this->createMock(MatchDataService::class),
            $this->createMock(InfosService::class)
        );

        $this->assertEquals('130000', $playerServiceTest->coutTotalJoueurs($equipeMock));
    }

    /**
     * @test
     */
    public function le_cout_total_des_joueurs_est_bien_calcule_bb2020(): void
    {
        $equipeMock = $this->createMock(Teams::class);

        $baseSkillsTest = new ArrayCollection();

        $positionMock = $this->createMock(GameDataPlayersBb2020::class);
        $positionMock->method('getCost')->willReturn(110_000);
        $positionMock->method('getBaseSkills')->willReturn($baseSkillsTest);

        $gameDataSkillMock = $this->createMock(GameDataSkillsBb2020::class);

        $gameDataSkillsMockDisposable = $this->createMock(GameDataSkillsBb2020::class);
        $gameDataSkillsMockFanFavorite = $this->createMock(GameDataSkillsBb2020::class);
        $gameDataSkillsMockFanFavorite->method('getId')->willReturn(1);

        $gameDataSkillRepoMock = $this->getMockBuilder(GameDataSkillsBb2020::class)
            ->addMethods(['findOneBy'])
            ->getMock();
        $gameDataSkillRepoMock->method('findOneBy')->willReturnOnConsecutiveCalls($gameDataSkillsMockDisposable, $gameDataSkillsMockFanFavorite);

        $playersSkillMock = $this->createMock(PlayersSkills::class);
        $playersSkillMock->method('getType')->willReturn('P');
        $playersSkillMock->method('getFSkillBb2020')->willReturn($gameDataSkillMock);

        $playerSkillsTest = new ArrayCollection();
        $playerSkillsTest->add($playersSkillMock);

        $joueurMock = $this->createMock(Players::class);
        $joueurMock->method('getFPosBb2020')->willReturn($positionMock);
        $joueurMock->method('getInjRpm')->willReturn(0);
        $joueurMock->method('getSkills')->willReturn($playerSkillsTest);
        $joueurMock->method('getRuleset')->willReturn(RulesetEnum::BB_2020);

        $playerRepoMock = $this->getMockBuilder(Players::class)
            ->addMethods(['listeDesJoueursPourlEquipe'])
            ->getMock();
        $playerRepoMock->method('listeDesJoueursPourlEquipe')->willReturn(
            [$joueurMock]
        );

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->will($this->returnCallback(
            function ($entityName) use ($playerRepoMock,$gameDataSkillRepoMock) {
                if ($entityName === 'App\Entity\Players') {
                    return $playerRepoMock;
                }

                if ($entityName === 'App\Entity\GameDataSkillsBb2020') {
                    return $gameDataSkillRepoMock;
                }

                if ($entityName === 'App\Entity\PlayersSkills') {
                    return $this->createMock(ObjectRepository::class);
                }

                return true;
            }
        ));

        $playerServiceTest = new PlayerService(
            $objectManager,
            $this->createMock(EquipeGestionService::class),
            $this->createMock(MatchDataService::class),
            $this->createMock(InfosService::class)
        );

        $this->assertEquals('130000', $playerServiceTest->coutTotalJoueurs($equipeMock));
    }
}