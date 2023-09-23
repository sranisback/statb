<?php


namespace App\Tests\src\Service\EquipeService;


use App\Entity\Races;
use App\Entity\RacesBb2020;
use App\Entity\SpecialRule;
use App\Entity\Teams;
use App\Enum\RulesetEnum;
use App\Service\EquipeGestionService;
use App\Service\EquipeService;
use App\Service\InducementService;
use App\Service\PlayerService;
use App\Service\SettingsService;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;

class calculsInducementEquipeTest extends TestCase
{
    /**
     * @test
     */
    public function les_inducements_sont_calcules_bb2016()
    {
        $raceMock = $this->createMock(Races::class);
        $raceMock->method('getCostRr')->willReturn(50000);

        $equipeMock = $this->createMock(Teams::class);
        $equipeMock->method('getFRace')->willReturn($raceMock);
        $equipeMock->method('getRuleset')->willReturn(RulesetEnum::BB_2016);
        $equipeMock->method('getRerolls')->willReturn(0);

        $playerServiceMock = $this->createMock(PlayerService::class);
        $playerServiceMock->method('coutTotalJoueurs')->willReturn(500_000);

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

        $equipeGestionServiceMock = $this->createMock(EquipeGestionService::class);
        $equipeGestionServiceMock->method('tvDelEquipe')->willReturn(500_000);

        $equipeServiceTest = new EquipeService(
            $this->createMock(EntityManager::class),
            $this->createMock(SettingsService::class),
            $inducementServiceMock,
            $equipeGestionServiceMock
        );

        $attendu = [
            'playersCost' => 500_000,
            'rerolls' => 0,
            'pop' => 0,
            'asscoaches' => 0,
            'cheerleader' => 0,
            'apo' => 0,
            'tv' => 500_000
        ];

        $this->assertEquals($attendu, $equipeServiceTest->calculsInducementEquipe($equipeMock,$playerServiceMock));
    }

    /**
     * @test
     */
    public function les_inducements_sont_calcules_bb2020()
    {
        $specialRule = new SpecialRule();
        $specialRule->setName("TEST SPERULE");

        $race = new RacesBb2020();
        $race->setCostRr(50000);
        $race->addSpecialRule($specialRule);

        $equipe = new Teams();
        $equipe->setRace($race);
        $equipe->setRuleset(RulesetEnum::BB_2020);

        $playerServiceMock = $this->createMock(PlayerService::class);
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

        $equipeGestionServiceMock = $this->createMock(EquipeGestionService::class);
        $equipeGestionServiceMock->method('tvDelEquipe')->willReturn(500_000);

        $equipeServiceTest = new EquipeService(
            $this->createMock(EntityManager::class),
            $this->createMock(SettingsService::class),
            $inducementServiceMock,
            $equipeGestionServiceMock
        );

        $attendu = [
            'playersCost' => 500000,
            'rerolls' => 0,
            'pop' => 0,
            'asscoaches' => 0,
            'cheerleader' => 0,
            'apo' => 0,
            'tv' => 500000,
            'reglesSpeciales' => 'TEST SPERULE'
        ];

        $this->assertEquals($attendu, $equipeServiceTest->calculsInducementEquipe($equipe,$playerServiceMock));
    }
}