<?php


namespace App\Tests\src\Service\EquipeService;


use App\Entity\Players;
use App\Entity\Races;
use App\Entity\Teams;
use App\Service\EquipeGestionService;
use App\Service\EquipeService;
use App\Service\InducementService;
use App\Service\PlayerService;
use App\Service\SettingsService;
use App\Tests\src\TestServiceFactory\EquipeServiceTestFactory;
use Doctrine\Common\Collections\ArrayCollection;
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

        $joueur0 = new Players();
        $joueur1 = new Players();
        $joueur2 = new Players();
        $joueur3 = new Players();
        $joueur4 = new Players();

        $joueurCollection = new ArrayCollection();
        $joueurCollection->add($joueur0);
        $joueurCollection->add($joueur1);
        $joueurCollection->add($joueur2);
        $joueurCollection->add($joueur3);
        $joueurCollection->add($joueur4);

        $race = new Races();
        $race->setCostRr(50000);

        $equipe = new Teams();
        $equipe->setFRace($race);
        $equipe->setJoueurs($joueurCollection);

        $settingServiceMock = $this->createMock(SettingsService::class);
        $settingServiceMock->method('anneeCourante')->willReturn(2);

        $playerServiceMock = $this->createMock(PlayerService::class);
        $playerServiceMock->method('ligneJoueur')->willReturn([$pData,$pData,$pData,$pData,$pData]);
        $playerServiceMock->method('coutTotalJoueurs')->willReturn(500000);

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

        $equipeGestionServiceMock = $this->createMock(EquipeGestionService::class);
        $equipeGestionServiceMock->method('tvDelEquipe')->willReturn(500_000);

        $equipeServiceTest = (new EquipeServiceTestFactory)->getInstance(
            $objectManager,
            $settingServiceMock,
            $inducementServiceMock,
            $equipeGestionServiceMock,
            $playerServiceMock
        );

        $attendu = [
            'players' => $joueurCollection,
            'team' => $equipe,
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
                5 => 'Résidence'
            ],
            'compteur' => [
                'actif' => 5,
                'journalier' => 0,
                'blesses' => 0
            ]
        ];

        $this->assertEquals($attendu, $equipeServiceTest->feuilleDequipeComplete($equipe));
    }

}