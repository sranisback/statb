<?php

namespace App\Tests\src\Service\SettingService;


use App\Entity\Coaches;
use App\Entity\GameDataStadium;
use App\Entity\Races;
use App\Entity\Stades;
use App\Factory\TeamsFactory;
use App\Service\StadeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class construireStadeTest extends KernelTestCase
{
    /**
     * @test
     */
    public function le_stade_est_construit_avec_la_tresorerie()
    {
        $stade = new Stades();
        $stade->setNiveau(0);

        $race = new Races();
        $coach = new Coaches();

        $equipe = (new TeamsFactory)->lancerEquipe(
            1000000,
            'Test',
            150,
            $stade,
            4,
            $race,
            $coach
        );

        $typeStade = new GameDataStadium();

        $objectManager = $this->createMock(EntityManagerInterface::class);

        $stadeService = new StadeService($objectManager);

        $this->assertTrue($stadeService->construireStade($equipe, 'Nouveau Stade', $typeStade, 3));
        $this->assertEquals('500000', $equipe->getTreasury());
        $this->assertEquals('Nouveau Stade', $stade->getNom());
    }

    /**
     * @test
     */
    public function le_stade_est_achete_avec_les_economies()
    {
        $stade = new Stades();
        $stade->setNiveau(0);
        $stade->setTotalPayement(500000);

        $race = new Races();
        $coach = new Coaches();

        $equipe = (new TeamsFactory)->lancerEquipe(
            0,
            'Test',
            150,
            $stade,
            4,
            $race,
            $coach
        );

        $typeStade = new GameDataStadium();

        $objectManager = $this->createMock(EntityManagerInterface::class);

        $stadeService = new StadeService($objectManager);

        $this->assertTrue($stadeService->construireStade($equipe, 'Nouveau Stade', $typeStade, 3));
        $this->assertEquals('0', $stade->getTotalPayement());
        $this->assertEquals('Nouveau Stade', $stade->getNom());
    }

    /**
     * @test
     */
    public function le_stade_est_achete_avec_les_economies_et_la_tresorerie()
    {
        $stade = new Stades();
        $stade->setNiveau(0);
        $stade->setTotalPayement(250000);

        $race = new Races();
        $coach = new Coaches();

        $equipe = (new TeamsFactory)->lancerEquipe(
            250000,
            'Test',
            150,
            $stade,
            4,
            $race,
            $coach
        );

        $typeStade = new GameDataStadium();

        $objectManager = $this->createMock(EntityManagerInterface::class);

        $stadeService = new StadeService($objectManager);

        $this->assertTrue($stadeService->construireStade($equipe, 'Nouveau Stade', $typeStade, 3));
        $this->assertEquals('0', $stade->getTotalPayement());
        $this->assertEquals('0', $equipe->getTreasury());
        $this->assertEquals('Nouveau Stade', $stade->getNom());
    }

    /**
     * @test
     */
    public function le_stade_est_upgrade()
    {
        $stade = new Stades();
        $stade->setNiveau(1);
        $stade->setTotalPayement(50000);
        $stade->setNom('Nouveau Stade');

        $race = new Races();
        $coach = new Coaches();

        $equipe = (new TeamsFactory)->lancerEquipe(
            50000,
            'Test',
            150,
            $stade,
            4,
            $race,
            $coach
        );

        $typeStade = new GameDataStadium();

        $objectManager = $this->createMock(EntityManagerInterface::class);

        $stadeService = new StadeService($objectManager);

        $this->assertTrue($stadeService->construireStade($equipe, 'Nouveau Stade', $typeStade, 2));
        $this->assertEquals('0', $stade->getTotalPayement());
        $this->assertEquals('0', $equipe->getTreasury());
        $this->assertEquals('Nouveau Stade', $stade->getNom());
    }

    /**
     * @test
     */
    public function il_reste_de_la_cagnotte()
    {
        $stade = new Stades();
        $stade->setNiveau(1);
        $stade->setTotalPayement(150000);
        $stade->setNom('Nouveau Stade');

        $race = new Races();
        $coach = new Coaches();

        $equipe = (new TeamsFactory)->lancerEquipe(
            0,
            'Test',
            150,
            $stade,
            4,
            $race,
            $coach
        );

        $typeStade = new GameDataStadium();

        $objectManager = $this->createMock(EntityManagerInterface::class);

        $stadeService = new StadeService($objectManager);

        $this->assertTrue($stadeService->construireStade($equipe, 'Nouveau Stade', $typeStade, 2));
        $this->assertEquals('50000', $stade->getTotalPayement());
        $this->assertEquals('Nouveau Stade', $stade->getNom());
    }

    /**
     * @test
     */
    public function le_stade_est_downgrade()
    {
        $stade = new Stades();
        $stade->setNiveau(2);
        $stade->setTotalPayement(150000);
        $stade->setNom('Nouveau Stade');

        $race = new Races();
        $coach = new Coaches();

        $equipe = (new TeamsFactory)->lancerEquipe(
            50000,
            'Test',
            150,
            $stade,
            4,
            $race,
            $coach
        );

        $typeStade = new GameDataStadium();

        $objectManager = $this->createMock(EntityManagerInterface::class);

        $stadeService = new StadeService($objectManager);

        $this->assertFalse($stadeService->construireStade($equipe, 'Nouveau Stade', $typeStade, 1));
        $this->assertEquals('150000', $stade->getTotalPayement());
        $this->assertEquals('50000', $equipe->getTreasury());
        $this->assertEquals('Nouveau Stade', $stade->getNom());
    }
}