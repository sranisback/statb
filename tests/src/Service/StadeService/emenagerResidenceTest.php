<?php


namespace App\Tests\src\Service\StadeService;


use App\Entity\Coaches;
use App\Entity\GameDataStadium;
use App\Entity\Races;
use App\Entity\RacesBb2020;
use App\Entity\Stades;
use App\Enum\RulesetEnum;
use App\Factory\TeamsFactory;
use App\Service\StadeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class emenagerResidenceTest extends KernelTestCase
{
    /**
     * @test
     */
    public function une_residence_est_emenagee_bb2016(): void
    {
        $stade = new Stades();
        $stade->setNiveau(0);

        $race = new Races();
        $coach = new Coaches();

        $equipe = TeamsFactory::lancerEquipe(
            1_000_000,
            'Test',
            150,
            $stade,
            4,
            $race,
            $coach,
            RulesetEnum::BB_2016
        );

        $typeStade = new GameDataStadium();

        $objectManager = $this->createMock(EntityManagerInterface::class);

        $stadeService = new StadeService($objectManager);

        $this->assertTrue($stadeService->emenagerResidence($equipe, 'Nouveau Stade', $typeStade));
        $this->assertEquals('1000000', $equipe->getTreasury());
        $this->assertEquals('Nouveau Stade', $stade->getNom());
    }

    /**
     * @test
     */
    public function une_residence_est_emenagee_bb2020(): void
    {
        $stade = new Stades();
        $stade->setNiveau(0);

        $race = new RacesBb2020();
        $coach = new Coaches();

        $equipe = TeamsFactory::lancerEquipe(
            1_000_000,
            'Test',
            150,
            $stade,
            4,
            $race,
            $coach,
            RulesetEnum::BB_2020
        );

        $typeStade = new GameDataStadium();

        $objectManager = $this->createMock(EntityManagerInterface::class);

        $stadeService = new StadeService($objectManager);

        $this->assertTrue($stadeService->emenagerResidence($equipe, 'Nouveau Stade', $typeStade));
        $this->assertEquals('1000000', $equipe->getTreasury());
        $this->assertEquals('Nouveau Stade', $stade->getNom());
    }
}