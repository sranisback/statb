<?php


namespace App\Tests\src\Service\EquipeService;


use App\Entity\RacesBb2020;
use App\Entity\SpecialRule;
use App\Entity\Teams;
use App\Service\EquipeGestionService;
use App\Service\EquipeService;
use App\Service\InducementService;
use App\Service\SettingsService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class ParseReglesSpecialesTest extends TestCase
{

    /**
     * @test
     */
    public function les_regles_speciales_sont_bien_parsee()
    {
        $specialRule1 = new SpecialRule();
        $specialRule1->setName("TEST REGLES SPECIALE 1");

        $specialRule2 = new SpecialRule();
        $specialRule2->setName("TEST REGLES SPECIALE 2");

        $race = new RacesBb2020();
        $race->addSpecialRule($specialRule1);
        $race->addSpecialRule($specialRule2);

        $equipe = new Teams();
        $equipe->setRace($race);

        $equipeService = new EquipeService(
            $this->createMock(EntityManagerInterface::class),
            $this->createMock(SettingsService::class),
            $this->createMock(InducementService::class),
            $this->createMock(EquipeGestionService::class)
        );

        $result = $equipeService->parseReglesSpeciales($equipe);

        $this->assertEquals('TEST REGLES SPECIALE 1, TEST REGLES SPECIALE 2', $result);
    }
}