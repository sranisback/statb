<?php


namespace App\Tests\src\Controller\ClassementController;


use App\DataFixtures\TeamFixture;
use App\Tests\src\Functionnal;

class cinqDernierMatchPourEquipeTest extends Functionnal
{
    /**
     * @test
     */
    public function les_cinq_derniers_matchs_par_equipes()
    {
        $teamFixture = new TeamFixture();
        $teamFixture->setReferenceRepository($this->referenceRepo);
        $equipe = $teamFixture->load($this->entityManager);

        $this->client->request('GET', '/cinqDernierMatchPourEquipe/' . $equipe->getTeamId());

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('Les derniers matchs',$this->client->getResponse());
    }
}