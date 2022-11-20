<?php

namespace App\Tests\src\Service\InducementService;

use App\Entity\Matches;
use App\Entity\Races;
use App\Entity\RacesBb2020;
use App\Entity\Stades;
use App\Entity\Teams;
use App\Enum\RulesetEnum;
use App\Service\EquipeGestionService;
use App\Service\EquipeService;
use App\Service\InducementService;
use App\Service\PlayerService;
use App\Tests\src\TestServiceFactory\InducementServiceTestFactory;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AjoutInducementTest extends KernelTestCase
{
     /**
     * @test
     */
    public function le_cout_des_rr_change_quand_l_equipe_a_un_match_bb2016()
    {
        $raceTest = new Races();
        $raceTest->setCostRr(50_000);

        $equipeTest = new Teams();
        $equipeTest->setFRace($raceTest);
        $equipeTest->setTreasury(100000);
        $equipeTest->setRerolls(0);
        $equipeTest->setRuleset(RulesetEnum::BB_2016);

        $matchTest = new Matches();

        $matchRepoMock = $this->getMockBuilder(Matches::class)->addMethods(['listeDesMatchs'])->getMock();
        $matchRepoMock->method('listeDesMatchs')->willReturn([$matchTest]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchRepoMock);

        $inducementServiceTest = (new InducementServiceTestFactory)->getInstance(
            $objectManager
        );

        $resultatAttendu = [
            'inducost' => '100000',
            'nbr' => 1
        ];

        $this->assertEquals($resultatAttendu, $inducementServiceTest ->ajoutInducement(
            $equipeTest,
            'rr'
        ));

        $this->assertEquals(0, $equipeTest->getTreasury());
        $this->assertEquals(1, $equipeTest->getRerolls());
    }

    /**
     * @test
     */
    public function le_cout_des_rr_change_quand_l_equipe_a_un_match_bb2020()
    {
        $raceTest = new RacesBb2020();
        $raceTest->setCostRr(50_000);

        $equipeTest = new Teams();
        $equipeTest->setRace($raceTest);
        $equipeTest->setTreasury(100000);
        $equipeTest->setRerolls(0);
        $equipeTest->setRuleset(RulesetEnum::BB_2020);

        $matchTest = new Matches();

        $matchRepoMock = $this->getMockBuilder(Matches::class)->addMethods(['listeDesMatchs'])->getMock();
        $matchRepoMock->method('listeDesMatchs')->willReturn([$matchTest]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchRepoMock);

        $inducementServiceTest = (new InducementServiceTestFactory)->getInstance(
            $objectManager
        );

        $resultatAttendu = [
            'inducost' => '100000',
            'nbr' => 1
        ];

        $this->assertEquals($resultatAttendu, $inducementServiceTest->ajoutInducement(
            $equipeTest,
            'rr'
        ));

        $this->assertEquals(0, $equipeTest->getTreasury());
        $this->assertEquals(1, $equipeTest->getRerolls());
    }

    /**
     * @test
     */
    public function la_pop_ne_monte_plus_apres_un_match_bb2016()
    {
        $raceTest = new Races();
        $raceTest->setCostRr(50_000);

        $equipeTest = new Teams();
        $equipeTest->setFRace($raceTest);
        $equipeTest->setTreasury(10000);
        $equipeTest->setFfBought(2);
        $equipeTest->setRuleset(RulesetEnum::BB_2016);

        $matchTest = new Matches();

        $matchRepoMock = $this->getMockBuilder(Matches::class)->addMethods(['listeDesMatchs'])->getMock();
        $matchRepoMock->method('listeDesMatchs')->willReturn([$matchTest]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchRepoMock);

        $inducementServiceTest = (new InducementServiceTestFactory)->getInstance(
            $objectManager
        );

        $resultatAttendu = [
            'inducost' => 0,
            'nbr' => 2
        ];

        $this->assertEquals($resultatAttendu, $inducementServiceTest->ajoutInducement(
            $equipeTest,
            'pop'
        ));

        $this->assertEquals(2, $equipeTest->getFfBought());
        $this->assertEquals(10000, $equipeTest->getTreasury());
    }

    /**
     * @test
     */
    public function la_pop_ne_monte_plus_apres_un_match_bb2020()
    {
        $raceTest = new RacesBb2020();
        $raceTest->setCostRr(50_000);

        $equipeTest = new Teams();
        $equipeTest->setRace($raceTest);
        $equipeTest->setTreasury(10000);
        $equipeTest->setFfBought(2);
        $equipeTest->setRuleset(RulesetEnum::BB_2020);

        $matchTest = new Matches();

        $matchRepoMock = $this->getMockBuilder(Matches::class)->addMethods(['listeDesMatchs'])->getMock();
        $matchRepoMock->method('listeDesMatchs')->willReturn([$matchTest]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchRepoMock);

        $inducementServiceTest = (new InducementServiceTestFactory)->getInstance(
            $objectManager
        );

        $resultatAttendu = [
            'inducost' => 0,
            'nbr' => 2
        ];

        $this->assertEquals($resultatAttendu, $inducementServiceTest->ajoutInducement(
            $equipeTest,
            'pop'
        ));

        $this->assertEquals(2, $equipeTest->getFfBought());
        $this->assertEquals(10000, $equipeTest->getTreasury());
    }

    /**
     * @test
     */
    public function le_cout_des_rr_avant_matchs_bb2016()
    {
        $raceTest = new Races();
        $raceTest->setCostRr(50_000);

        $equipeTest = new Teams();
        $equipeTest->setFRace($raceTest);
        $equipeTest->setTreasury(50_000);
        $equipeTest->setRerolls(0);
        $equipeTest->setRuleset(RulesetEnum::BB_2016);

        $matchRepoMock = $this->getMockBuilder(Matches::class)->addMethods(['listeDesMatchs'])->getMock();
        $matchRepoMock->method('listeDesMatchs')->willReturn([]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchRepoMock);

        $inducementServiceTest = (new InducementServiceTestFactory)->getInstance(
            $objectManager
        );

        $resultatAttendu = [
            'inducost' => 50_000,
            'nbr' => 1
        ];

        $this->assertEquals($resultatAttendu, $inducementServiceTest->ajoutInducement(
            $equipeTest,
            'rr'
        ));

        $this->assertEquals(0, $equipeTest->getTreasury());
        $this->assertEquals(1, $equipeTest->getRerolls());
    }

    /**
     * @test
     */
    public function le_cout_des_rr_avant_matchs_bb2020()
    {
        $raceTest = new RacesBb2020();
        $raceTest->setCostRr(50_000);

        $equipeTest = new Teams();
        $equipeTest->setRace($raceTest);
        $equipeTest->setTreasury(50_000);
        $equipeTest->setRerolls(0);
        $equipeTest->setRuleset(RulesetEnum::BB_2020);

        $matchRepoMock = $this->getMockBuilder(Matches::class)->addMethods(['listeDesMatchs'])->getMock();
        $matchRepoMock->method('listeDesMatchs')->willReturn([]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchRepoMock);

        $inducementServiceTest = (new InducementServiceTestFactory)->getInstance(
            $objectManager
        );

        $resultatAttendu = [
            'inducost' => 50_000,
            'nbr' => 1
        ];

        $this->assertEquals($resultatAttendu, $inducementServiceTest->ajoutInducement(
            $equipeTest,
            'rr'
        ));

        $this->assertEquals(0, $equipeTest->getTreasury());
        $this->assertEquals(1, $equipeTest->getRerolls());
    }

    /**
     * @test
     */
    public function achat_de_la_pop()
    {
        $raceTest = new RacesBb2020();
        $raceTest->setCostRr(50_000);

        $equipeTest = new Teams();
        $equipeTest->setTreasury(10_000);
        $equipeTest->setRerolls(0);
        $equipeTest->setRace($raceTest);
        $equipeTest->setRuleset(RulesetEnum::BB_2020);

        $matchRepoMock = $this->getMockBuilder(Matches::class)->addMethods(['listeDesMatchs'])->getMock();
        $matchRepoMock->method('listeDesMatchs')->willReturn([]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchRepoMock);

        $inducementServiceTest = (new InducementServiceTestFactory)->getInstance(
            $objectManager
        );

        $resultatAttendu = [
            'inducost' => 10000,
            'nbr' => 1
        ];

        $this->assertEquals($resultatAttendu, $inducementServiceTest->ajoutInducement(
            $equipeTest,
            'pop'
        ));

        $this->assertEquals(1, $equipeTest->getFfBought());
        $this->assertEquals(0, $equipeTest->getTreasury());
    }

    /**
     * @test
     */
    public function achat_cheerleaders()
    {
        $raceTest = new RacesBb2020();
        $raceTest->setCostRr(50_000);

        $equipeTest = new Teams();
        $equipeTest->setTreasury(10_000);
        $equipeTest->setRerolls(0);
        $equipeTest->setRace($raceTest);
        $equipeTest->setRuleset(RulesetEnum::BB_2020);

        $matchRepoMock = $this->getMockBuilder(Matches::class)->addMethods(['listeDesMatchs'])->getMock();
        $matchRepoMock->method('listeDesMatchs')->willReturn([]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchRepoMock);

        $inducementServiceTest = (new InducementServiceTestFactory)->getInstance(
            $objectManager
        );

        $resultatAttendu = [
            'inducost' => 10000,
            'nbr' => 1
        ];

        $this->assertEquals($resultatAttendu, $inducementServiceTest->ajoutInducement(
            $equipeTest,
            'chl'
        ));

        $this->assertEquals(1, $equipeTest->getCheerleaders());
        $this->assertEquals(0, $equipeTest->getTreasury());
    }

    /**
     * @test
     */
    public function achat_assistantCoach()
    {
        $raceTest = new RacesBb2020();
        $raceTest->setCostRr(50_000);

        $equipeTest = new Teams();
        $equipeTest->setTreasury(10_000);
        $equipeTest->setRerolls(0);
        $equipeTest->setRace($raceTest);
        $equipeTest->setRuleset(RulesetEnum::BB_2020);

        $matchRepoMock = $this->getMockBuilder(Matches::class)->addMethods(['listeDesMatchs'])->getMock();
        $matchRepoMock->method('listeDesMatchs')->willReturn([]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchRepoMock);

        $inducementServiceTest = (new InducementServiceTestFactory)->getInstance(
            $objectManager
        );

        $resultatAttendu = [
            'inducost' => 10000,
            'nbr' => 1
        ];

        $this->assertEquals($resultatAttendu, $inducementServiceTest->ajoutInducement(
            $equipeTest,
            'ac'
        ));

        $this->assertEquals(1, $equipeTest->getAssCoaches());
        $this->assertEquals(0, $equipeTest->getTreasury());
    }
    
    /**
     * @test 
     */
    public function achat_apo()
    {
        $raceTest = new RacesBb2020();
        $raceTest->setCostRr(50_000);

        $equipeTest = new Teams();
        $equipeTest->setTreasury(50_000);
        $equipeTest->setRerolls(0);
        $equipeTest->setRace($raceTest);
        $equipeTest->setRuleset(RulesetEnum::BB_2020);

        $matchRepoMock = $this->getMockBuilder(Matches::class)->addMethods(['listeDesMatchs'])->getMock();
        $matchRepoMock->method('listeDesMatchs')->willReturn([]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchRepoMock);

        $inducementServiceTest = (new InducementServiceTestFactory)->getInstance(
            $objectManager
        );

        $resultatAttendu = [
            'inducost' => 50_000,
            'nbr' => 1
        ];

        $this->assertEquals($resultatAttendu, $inducementServiceTest->ajoutInducement(
            $equipeTest,
            'apo'
        ));

        $this->assertEquals(1, $equipeTest->getApothecary());
        $this->assertEquals(0, $equipeTest->getTreasury());
    }

    /**
     * @test
     */
    public function ajout_paiement_stade()
    {
        $raceTest = new RacesBb2020();
        $raceTest->setCostRr(50_000);

        $stadeTest = new Stades();

        $equipeTest = new Teams();
        $equipeTest->setTreasury(70_000);
        $equipeTest->setRerolls(0);
        $equipeTest->setRace($raceTest);
        $equipeTest->setRuleset(RulesetEnum::BB_2020);
        $equipeTest->setFStades($stadeTest);

        $matchRepoMock = $this->getMockBuilder(Matches::class)->addMethods(['listeDesMatchs'])->getMock();
        $matchRepoMock->method('listeDesMatchs')->willReturn([]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchRepoMock);

        $inducementServiceTest = (new InducementServiceTestFactory)->getInstance(
            $objectManager
        );

        $resultatAttendu = [
            'inducost' => 70_000,
            'nbr' => 50_000
        ];

        $this->assertEquals($resultatAttendu, $inducementServiceTest->ajoutInducement(
            $equipeTest,
            'pay'
        ));

        $this->assertEquals(0, $equipeTest->getTreasury());
        $this->assertEquals(50_000,$equipeTest->getFStades()->getTotalPayement());
    }
}