<?php


namespace App\Tests\src\Service\EquipeService;


use App\Entity\Players;
use App\Service\EquipeService;
use App\Service\PlayerService;
use App\Service\SettingsService;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;

class ligneJoueurTest extends TestCase
{
    /**
     * @test
     */
    public function une_ligne_est_generee()
    {
        $retourTableStat = [
            'NbrMatch' => 0,
            'cp' => 0,
            'td' => 0,
            'int' => 0,
            'cas' => 0,
            'mvp' => 0,
            'agg' => 0
        ];

        $joueurMock = $this->createMock(Players::class);

        $playerServiceMock = $this->createMock(PlayerService::class);
        $playerServiceMock->method('statsDuJoueur')->willReturn(['comp' => '', 'actions' => $retourTableStat]);
        $playerServiceMock->method('xpDuJoueur')->willReturn(0);
        $playerServiceMock->method('valeurDunJoueur')->willReturn(50000);
        $playerServiceMock->method('statutDuJoueur')->willReturn('');
        $playerServiceMock->method('coutTotalJoueurs')->willReturn(50000);
        $playerServiceMock->method('coutTotalJoueurs')->willReturn(50000);

        $equipeServiceTest = new EquipeService(
            $this->createMock(EntityManager::class),
            $this->createMock(SettingsService::class)
        );

        $attendu = [
            [
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
            ]
        ];

        $this->assertEquals($attendu, $equipeServiceTest->ligneJoueur([$joueurMock], $playerServiceMock));
    }

    /**
     * @test
     */
    public function il_n_y_a_pas_de_donnees()
    {
        $playerServiceMock = $this->createMock(PlayerService::class);

        $equipeServiceTest = new EquipeService(
            $this->createMock(EntityManager::class),
            $this->createMock(SettingsService::class)
        );

        $attendu = [];

        $this->assertEquals($attendu, $equipeServiceTest->ligneJoueur([], $playerServiceMock));
    }
}