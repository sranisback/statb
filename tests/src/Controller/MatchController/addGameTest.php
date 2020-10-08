<?php


namespace App\Tests\src\Controller\MatchController;


use App\DataFixtures\MeteoFixture;
use App\DataFixtures\StadesFixture;
use App\DataFixtures\TeamFixture;
use App\Tests\src\Functionnal;

class addGameTest extends Functionnal
{
    /**
     * @test
     */
    public function un_json_est_retournee()
    {
        $equipeFixture = new TeamFixture();
        $equipeFixture->setReferenceRepository($this->referenceRepo);
        $equipe0Test = $equipeFixture->load($this->entityManager);
        $equipe1Test = $equipeFixture->load($this->entityManager);

        $stadeFixture = new StadesFixture();
        $stadeTest = $stadeFixture->load($this->entityManager);

        $meteoFixture = new MeteoFixture();
        $meteoTest = $meteoFixture->load($this->entityManager);

        $table =  [
            'team_1' => $equipe0Test->getTeamId(),
            'team_2' => $equipe1Test->getTeamId(),
            'stade' => $stadeTest->getFTypeStade()->getId(),
            'stadeAccueil' => 3,
            'meteo' => $meteoTest->getId(),
            'totalpop' => 0,
            'varpop_team1' => 1,
            'varpop_team2' => 0,
            'gain1' => 10000,
            'gain2' => 20000,
            'score1' => 1,
            'score2' => 0,
            'depense1' => 0,
            'depense2' => 0,
            'player' => []
        ];

        $this->client->request('POST', '/addGame',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($table));

        $this->assertResponseIsSuccessful();
    }
}