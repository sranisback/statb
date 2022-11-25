<?php


namespace App\Tests\src\Service\PlayerService;

use App\Entity\GameDataPlayers;
use App\Entity\GameDataSkills;
use App\Entity\MatchData;
use App\Entity\Players;
use App\Entity\PlayersSkills;
use App\Tests\src\TestServiceFactory\PlayerServiceTestFactory;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;

class ligneJoueurTest extends TestCase
{
    /**
     * @test
     */
    public function une_ligne_est_generee_bb2016()
    {
        $baseSkillsTest = new ArrayCollection();

        $gameDataSkillMock = $this->createMock(GameDataSkills::class);

        $positionMock = $this->createMock(GameDataPlayers::class);
        $positionMock->method('getBaseSkills')->willReturn($baseSkillsTest);

        $joueurMock = $this->createMock(Players::class);
        $joueurMock->method('getPlayerId')->willReturn(1);
        $joueurMock->method('getJournalier')->willReturn(false);
        $joueurMock->method('getFpos')->willReturn($positionMock);
        $joueurMock->method('getSkills')->willReturn($gameDataSkillMock);
        $joueurMock->method('getRuleset')->willReturn(0);

        $matchDataRepoMock = $this->createMock(ObjectRepository::class);
        $matchDataRepoMock->method('findBy')->willReturn([]);

        $gameDataSkillsMockDisposable = $this->createMock(GameDataSkills::class);

        $gameDataSkillRepoMock = $this->getMockBuilder(Players::class)
            ->addMethods(['findOneBy'])
            ->getMock();
        $gameDataSkillRepoMock->method('findOneBy')->willReturn($gameDataSkillsMockDisposable);

        $playersSkillsRepoMock = $this->createMock(ObjectRepository::class);
        $playersSkillsRepoMock->method('findBy')->willReturn(false);

        $objectManager = $this->createMock(EntityManager::class);
        $objectManager->method('getRepository')->will(
            $this->returnCallback(
                function ($entityName) use ($matchDataRepoMock, $gameDataSkillRepoMock, $playersSkillsRepoMock) {
                    if ($entityName === MatchData::class) {
                        return $matchDataRepoMock;
                    }

                    if ($entityName === 'App\Entity\GameDataSkills') {
                        return $gameDataSkillRepoMock;
                    }

                    if ($entityName === PlayersSkills::class) {
                        return $playersSkillsRepoMock;
                    }
                    return true;
                }
            )
        );

        $playerServiceTest = (new PlayerServiceTestFactory)->getInstance(
            $objectManager,
        );

        $attendu = [
            [
                'pid' => 1,
                'nbrm' => 0,
                'cp' => 0,
                'td' => 0,
                'int' => 0,
                'cas' => 0,
                'mvp' => 0,
                'agg' => 0,
                'skill' => '',
                'spp' => 0,
                'cost' => 0,
                'status' => '',
                'bonus' => 0,
                'exp' => 0
            ]
        ];

        $this->assertEquals($attendu, $playerServiceTest->ligneJoueur([$joueurMock]));
    }

    /**
     * @test
     */
    public function il_n_y_a_pas_de_donnees()
    {
        $joueurMock = $this->createMock(Players::class);
        $joueurMock->method('getPlayerId')->willReturn(1);
        $joueurMock->method('getJournalier')->willReturn(false);

        $playerServiceTest = (new PlayerServiceTestFactory)->getInstance();

        $attendu = [];

        $this->assertEquals($attendu, $playerServiceTest->ligneJoueur([]));
    }


    /**
     * @test
     */
    public function une_ligne_est_generee_bb2020()
    {
        $baseSkillsTest = new ArrayCollection();

        $gameDataSkillMock = $this->createMock(GameDataSkills::class);

        $positionMock = $this->createMock(GameDataPlayers::class);
        $positionMock->method('getBaseSkills')->willReturn($baseSkillsTest);

        $joueurMock = $this->createMock(Players::class);
        $joueurMock->method('getPlayerId')->willReturn(1);
        $joueurMock->method('getJournalier')->willReturn(false);
        $joueurMock->method('getFpos')->willReturn($positionMock);
        $joueurMock->method('getSkills')->willReturn($gameDataSkillMock);
        $joueurMock->method('getRuleset')->willReturn(1);

        $matchDataRepoMock = $this->createMock(ObjectRepository::class);
        $matchDataRepoMock->method('findBy')->willReturn([]);

        $gameDataSkillsMockDisposable = $this->createMock(GameDataSkills::class);

        $gameDataSkillRepoMock = $this->getMockBuilder(Players::class)
            ->addMethods(['findOneBy'])
            ->getMock();
        $gameDataSkillRepoMock->method('findOneBy')->willReturn($gameDataSkillsMockDisposable);

        $playersSkillsRepoMock = $this->createMock(ObjectRepository::class);
        $playersSkillsRepoMock->method('findBy')->willReturn(false);

        $objectManager = $this->createMock(EntityManager::class);
        $objectManager->method('getRepository')->will(
            $this->returnCallback(
                function ($entityName) use ($matchDataRepoMock, $gameDataSkillRepoMock, $playersSkillsRepoMock) {
                    if ($entityName === MatchData::class) {
                        return $matchDataRepoMock;
                    }

                    if ($entityName === 'App\Entity\GameDataSkills') {
                        return $gameDataSkillRepoMock;
                    }

                    if ($entityName === PlayersSkills::class) {
                        return $playersSkillsRepoMock;
                    }
                    return true;
                }
            )
        );

        $playerServiceTest = (new PlayerServiceTestFactory)->getInstance(
            $objectManager,
        );

        $attendu = [
            [
                'pid' => 1,
                'nbrm' => 0,
                'cp' => 0,
                'td' => 0,
                'int' => 0,
                'cas' => 0,
                'mvp' => 0,
                'agg' => 0,
                'skill' => '',
                'spp' => 0,
                'cost' => 0,
                'status' => '',
                'bonus' => 0,
                'exp' => 0
            ]
        ];

        $this->assertEquals($attendu, $playerServiceTest->ligneJoueur([$joueurMock]));
    }
}
