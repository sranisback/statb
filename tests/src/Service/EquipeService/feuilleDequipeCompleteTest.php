<?php


namespace App\Tests\src\Service\EquipeService;


use App\Entity\Players;
use App\Entity\Races;
use App\Entity\Teams;
use App\Service\EquipeGestionService;
use App\Service\EquipeService;
use App\Service\InducementService;
use App\Service\InfosService;
use App\Service\PlayerService;
use App\Service\SettingsService;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;

class feuilleDequipeCompleteTest extends TestCase
{
    /**
     * @test
     */
    public function une_feuille_d_equipe_est_generee()
    {
        $pData  = [
            'pid' => null,
            'nbrm' => 0,
            'cp' => 0,
            'td' => 0,
            'int' => 0,
            'cas' => 0,
            'mvp' => 0,
            'agg' => 0,
            'skill' => '',
            'spp' => 0,
            'cost' => 50000,
            'status' => ''
        ];

        $joueurMock0 = $this->createMock(Players::class);
        $joueurMock1 = $this->createMock(Players::class);
        $joueurMock2 = $this->createMock(Players::class);
        $joueurMock3 = $this->createMock(Players::class);
        $joueurMock4 = $this->createMock(Players::class);

        $raceMock = $this->createMock(Races::class);
        $raceMock->method('getCostRr')->willReturn(50000);

        $equipeMock = $this->createMock(Teams::class);
        $equipeMock->method('getFRace')->willReturn($raceMock);

        $settingServiceMock = $this->createMock(SettingsService::class);
        $settingServiceMock->method('anneeCourante')->willReturn(2);

        $playerServiceMock = $this->createMock(PlayerService::class);
        $playerServiceMock->method('ligneJoueur')->willReturn([$pData,$pData,$pData,$pData,$pData]);
        $playerServiceMock->method('coutTotalJoueurs')->willReturn(500000);

        $joueurRepoMock = $this->getMockBuilder(Players::class)
            ->addMethods(['listeDesJoueursPourlEquipe'])
            ->getMock();
        $joueurRepoMock->method('listeDesJoueursPourlEquipe')->willReturn(
            [$joueurMock0, $joueurMock1, $joueurMock2, $joueurMock3, $joueurMock4]
        );

        $inducementServiceMock = $this->createMock(InducementService::class);
        $inducementServiceMock->method('valeurInducementDelEquipe')->willReturn(
            [
                'rerolls' => 0,
                'pop' => 0,
                'asscoaches' => 0,
                'cheerleader' => 0,
                'apo' => 0,
                'total' => 0
            ]
        );

        $objectManager = $this->createMock(EntityManager::class);
        $objectManager->method('getRepository')->willReturn($joueurRepoMock);

        $equipeGestionServiceMock = $this->createMock(EquipeGestionService::class);
        $equipeGestionServiceMock->method('tvDelEquipe')->willReturn(500_000);

        $equipeServiceTest = new EquipeService(
            $objectManager,
            $settingServiceMock,
            $inducementServiceMock,
            $equipeGestionServiceMock
        );

        $attendu = [
            'players' => [$joueurMock0, $joueurMock1, $joueurMock2, $joueurMock3, $joueurMock4],
            'team' => $equipeMock,
            'pdata' => [
                $pData,
                $pData,
                $pData,
                $pData,
                $pData
            ],
            'tdata' => [
                'playersCost' => 500000,
                'rerolls' => 0,
                'pop' => 0,
                'asscoaches' => 0,
                'cheerleader' => 0,
                'apo' => 0,
                'tv' => 500000
            ],
            'annee' => 2,
            'niveauStade' => [
                0 => 'Prairie Verte',
                1 => 'Terrain aménagé',
                2 => 'Terrain bien aménagé',
                3 => 'Stade Correct',
                4 => 'Stade Ultra moderne',
                5 => 'Résidence',
            ]
        ];

        $this->assertEquals($attendu, $equipeServiceTest->feuilleDequipeComplete($equipeMock, $playerServiceMock));
    }

}