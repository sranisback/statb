<?php

namespace App\Tests\src\Service\InducementService;


use App\Entity\Matches;
use App\Entity\Races;
use App\Entity\RacesBb2020;
use App\Entity\Teams;
use App\Enum\RulesetEnum;
use App\Service\EquipeGestionService;
use App\Service\EquipeService;
use App\Service\InducementService;
use App\Service\InfosService;
use App\Service\PlayerService;
use App\Service\SettingsService;
use App\Tests\src\TestServiceFactory\InducementServiceTestFactory;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class supprInducementTest extends TestCase
{
    /**
     * @test
     */
    public function les_inducements_rendent_l_argent_si_pas_de_match_bb2016(): void
    {
        $raceTest = new Races();
        $raceTest->setCostRr(50_000);

        $equipeTest = new Teams();
        $equipeTest->setFRace($raceTest);
        $equipeTest->setRerolls(2);
        $equipeTest->setTreasury(0);
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

        $this->assertEquals($resultatAttendu, $inducementServiceTest->supprInducement(
            $equipeTest,
            'rr'
        ));

        $this->assertEquals(50_000, $equipeTest->getTreasury());
        $this->assertEquals(1, $equipeTest->getRerolls());
    }

    /**
     * @test
     */
    public function les_inducements_rendent_l_argent_si_pas_de_match_bb2020(): void
    {
        $raceTest = new RacesBb2020();
        $raceTest->setCostRr(50_000);

        $equipeTest = new Teams();
        $equipeTest->setRace($raceTest);
        $equipeTest->setRerolls(2);
        $equipeTest->setTreasury(0);
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

        $this->assertEquals($resultatAttendu, $inducementServiceTest->supprInducement(
            $equipeTest,
            'rr'
        ));

        $this->assertEquals(50_000, $equipeTest->getTreasury());
        $this->assertEquals(1, $equipeTest->getRerolls());
    }

    /**
     * @test
     */
    public function les_inducements_ne_rendent_pas_l_argent_si_match_bb2016(): void
    {
        $raceTest = new Races();
        $raceTest->setCostRr(50_000);

        $equipeTest = new Teams();
        $equipeTest->setFRace($raceTest);
        $equipeTest->setRerolls(2);
        $equipeTest->setTreasury(0);
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
            'inducost' => 50_000,
            'nbr' => 1
        ];

        $this->assertEquals($resultatAttendu, $inducementServiceTest->supprInducement(
            $equipeTest,
            'rr'
        ));

        $this->assertEquals(0, $equipeTest->getTreasury());
        $this->assertEquals(1, $equipeTest->getRerolls());
    }

    /**
     * @test
     */
    public function les_inducements_ne_rendent_pas_l_argent_si_match_bb2020(): void
    {
        $raceTest = new RacesBb2020();
        $raceTest->setCostRr(50_000);

        $equipeTest = new Teams();
        $equipeTest->setRace($raceTest);
        $equipeTest->setRerolls(2);
        $equipeTest->setTreasury(0);
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
            'inducost' => 50_000,
            'nbr' => 1
        ];

        $this->assertEquals($resultatAttendu, $inducementServiceTest->supprInducement(
            $equipeTest,
            'rr'
        ));

        $this->assertEquals(0, $equipeTest->getTreasury());
        $this->assertEquals(1, $equipeTest->getRerolls());
    }

    /**
     * @test
     */
    public function si_ffbought_est_a_zero_alors_on_prend_ff(): void
    {
        $raceTest = new RacesBb2020();
        $raceTest->setCostRr(50_000);

        $equipeTest = new Teams();
        $equipeTest->setRace($raceTest);
        $equipeTest->setff(1);
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

        $this->assertEquals($resultatAttendu, $inducementServiceTest->supprInducement(
            $equipeTest,
            'pop'
        ));

        $this->assertEquals(1, $equipeTest->getFf());
        $this->assertEquals(0, $equipeTest->getFfBought());
    }

    /**
     * @test
     */
    public function suppression_apo()
    {
        $raceTest = new RacesBb2020();
        $raceTest->setCostRr(50_000);

        $equipeTest = new Teams();
        $equipeTest->setTreasury(0);
        $equipeTest->setRerolls(0);
        $equipeTest->setRace($raceTest);
        $equipeTest->setRuleset(RulesetEnum::BB_2020);
        $equipeTest->setApothecary(1);

        $matchRepoMock = $this->getMockBuilder(Matches::class)->addMethods(['listeDesMatchs'])->getMock();
        $matchRepoMock->method('listeDesMatchs')->willReturn([]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchRepoMock);

        $inducementServiceTest = (new InducementServiceTestFactory)->getInstance(
            $objectManager
        );

        $resultatAttendu = [
            'inducost' => 50_000,
            'nbr' => 0
        ];

        $this->assertEquals($resultatAttendu, $inducementServiceTest->supprInducement(
            $equipeTest,
            'apo'
        ));

        $this->assertEquals(0, $equipeTest->getApothecary());
        $this->assertEquals(50_000, $equipeTest->getTreasury());
    }

    /**
     * @test
     */
    public function suppression_assistantCoach()
    {
        $raceTest = new RacesBb2020();
        $raceTest->setCostRr(50_000);

        $equipeTest = new Teams();
        $equipeTest->setTreasury(0);
        $equipeTest->setRerolls(0);
        $equipeTest->setRace($raceTest);
        $equipeTest->setRuleset(RulesetEnum::BB_2020);
        $equipeTest->setAssCoaches(1);

        $matchRepoMock = $this->getMockBuilder(Matches::class)->addMethods(['listeDesMatchs'])->getMock();
        $matchRepoMock->method('listeDesMatchs')->willReturn([]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchRepoMock);

        $inducementServiceTest = (new InducementServiceTestFactory)->getInstance(
            $objectManager
        );

        $resultatAttendu = [
            'inducost' => 10_000,
            'nbr' => 0
        ];

        $this->assertEquals($resultatAttendu, $inducementServiceTest->supprInducement(
            $equipeTest,
            'ac'
        ));

        $this->assertEquals(0, $equipeTest->getAssCoaches());
        $this->assertEquals(10_000, $equipeTest->getTreasury());
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
    public function suppression_cheerleaders()
    {
        $raceTest = new RacesBb2020();
        $raceTest->setCostRr(50_000);

        $equipeTest = new Teams();
        $equipeTest->setTreasury(0);
        $equipeTest->setRerolls(0);
        $equipeTest->setRace($raceTest);
        $equipeTest->setRuleset(RulesetEnum::BB_2020);
        $equipeTest->setCheerleaders(1);

        $matchRepoMock = $this->getMockBuilder(Matches::class)->addMethods(['listeDesMatchs'])->getMock();
        $matchRepoMock->method('listeDesMatchs')->willReturn([]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchRepoMock);

        $inducementServiceTest = (new InducementServiceTestFactory)->getInstance(
            $objectManager
        );

        $resultatAttendu = [
            'inducost' => 10_000,
            'nbr' => 0
        ];

        $this->assertEquals($resultatAttendu, $inducementServiceTest->supprInducement(
            $equipeTest,
            'chl'
        ));

        $this->assertEquals(0, $equipeTest->getCheerleaders());
        $this->assertEquals(10_000, $equipeTest->getTreasury());
    }

    /**
     * @test
     */
    public function suppression_de_la_pop_bb2016()
    {
        $raceTest = new RacesBb2020();
        $raceTest->setCostRr(50_000);

        $equipeTest = new Teams();
        $equipeTest->setTreasury(0);
        $equipeTest->setRerolls(0);
        $equipeTest->setRace($raceTest);
        $equipeTest->setRuleset(RulesetEnum::BB_2016);
        $equipeTest->setFfBought(1);

        $matchRepoMock = $this->getMockBuilder(Matches::class)->addMethods(['listeDesMatchs'])->getMock();
        $matchRepoMock->method('listeDesMatchs')->willReturn([]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchRepoMock);

        $inducementServiceTest = (new InducementServiceTestFactory)->getInstance(
            $objectManager
        );

        $resultatAttendu = [
            'inducost' => 10_000,
            'nbr' => 0
        ];

        $this->assertEquals($resultatAttendu, $inducementServiceTest->supprInducement(
            $equipeTest,
            'pop'
        ));

        $this->assertEquals(0, $equipeTest->getFfBought());
        $this->assertEquals(10_000, $equipeTest->getTreasury());
    }


    /**
     * @test
     */
    public function suppression_de_la_pop_bb2020()
    {
        $raceTest = new RacesBb2020();
        $raceTest->setCostRr(50_000);

        $equipeTest = new Teams();
        $equipeTest->setTreasury(0);
        $equipeTest->setRerolls(0);
        $equipeTest->setRace($raceTest);
        $equipeTest->setRuleset(RulesetEnum::BB_2020);
        $equipeTest->setFfBought(1);
        $equipeTest->setFf(1);

        $matchRepoMock = $this->getMockBuilder(Matches::class)->addMethods(['listeDesMatchs'])->getMock();
        $matchRepoMock->method('listeDesMatchs')->willReturn([]);

        $objectManager = $this->createMock(EntityManagerInterface::class);
        $objectManager->method('getRepository')->willReturn($matchRepoMock);

        $inducementServiceTest = (new InducementServiceTestFactory)->getInstance(
            $objectManager
        );

        $resultatAttendu = [
            'inducost' => 10_000,
            'nbr' => 1
        ];

        $this->assertEquals($resultatAttendu, $inducementServiceTest->supprInducement(
            $equipeTest,
            'pop'
        ));

        $this->assertEquals(0, $equipeTest->getFfBought());
        $this->assertEquals(1, $equipeTest->getFf());
        $this->assertEquals(10_000, $equipeTest->getTreasury());
    }
}