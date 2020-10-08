<?php


namespace App\Tests\src\Service\PlayerService;

use App\Entity\MatchData;
use App\Entity\Players;
use App\Entity\PlayersSkills;
use App\Service\EquipeService;
use App\Service\MatchDataService;
use App\Service\PlayerService;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;

class ligneJoueurTest extends TestCase
{
    /**
     * @test
     */
    public function une_ligne_est_generee()
    {
        $joueurMock = $this->createMock(Players::class);
        $joueurMock->method('getPlayerId')->willReturn(1);
        $joueurMock->method('getType')->willReturn(1);

        $playersSkillsRepoMock = $this->createMock(ObjectRepository::class);
        $playersSkillsRepoMock->method('findBy')->willReturn(false);

        $matchDataRepoMock = $this->createMock(ObjectRepository::class);
        $matchDataRepoMock->method('findBy')->willReturn([]);

        $objectManager = $this->createMock(EntityManager::class);
        $objectManager->method('getRepository')->will(
            $this->returnCallback(
                function ($entityName) use ($playersSkillsRepoMock,$matchDataRepoMock) {
                    if ($entityName === PlayersSkills::class) {
                        return $playersSkillsRepoMock;
                    }

                    if ($entityName === MatchData::class) {
                        return $matchDataRepoMock;
                    }
                    return true;
                }
            )
        );

        $equipeServiceMock = $this->createMock(EquipeService::class);
        $matchDataServiceMock = $this->createMock(MatchDataService::class);

        $playerServiceTest = new PlayerService(
            $objectManager,
            $equipeServiceMock,
            $matchDataServiceMock
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
                'status' => ''
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
        $joueurMock->method('getType')->willReturn(1);

        $playerServiceTest = new PlayerService(
            $this->createMock(EntityManager::class),
            $this->createMock(EquipeService::class),
            $this->createMock(MatchDataService::class)
        );

        $attendu = [];

        $this->assertEquals($attendu, $playerServiceTest->ligneJoueur([]));
    }
}
