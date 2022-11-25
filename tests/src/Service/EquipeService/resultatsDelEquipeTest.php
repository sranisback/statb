<?php


namespace App\Tests\src\Service\EquipeService;

use App\Entity\Matches;
use App\Entity\Teams;
use App\Tests\src\TestServiceFactory\EquipeServiceTestFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class resultatsDelEquipeTest extends KernelTestCase
{
    /**
     * @test
     */
    public function valider_resultats_equipe(): void
    {
        $equipeTest = new Teams();

        $matchTest0 = new Matches();
        $matchTest0->setTeam1Score(1);
        $matchTest0->setTeam2Score(0);
        $matchTest0->setTeam1($equipeTest);

        $matchTest1 = new Matches();
        $matchTest1->setTeam1Score(1);
        $matchTest1->setTeam2Score(1);
        $matchTest1->setTeam1($equipeTest);

        $matchTest2 = new Matches();
        $matchTest2->setTeam1Score(0);
        $matchTest2->setTeam2Score(1);
        $matchTest2->setTeam1($equipeTest);

        $equipeServiceTest = (new EquipeServiceTestFactory)->getInstance();

        $resultatAttendu = [
            'win' => 1,
            'loss' => 1,
            'draw' => 1
        ];

        $this->assertEquals($resultatAttendu, $equipeServiceTest->resultatsDelEquipe($equipeTest,[$matchTest0, $matchTest1, $matchTest2]));
    }
}