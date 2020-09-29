<?php


namespace App\Tests\src\Controller\ClassementController;


use App\DataFixtures\TeamFixture;
use App\Tests\src\Functionnal;

class tousLesMatchesPourEquipeTest extends Functionnal
{
    /**
     * @test
     */
    public function tous_les_matchs_sont_affiches()
    {
        $teamFixture = new TeamFixture();
        $teamFixture->setReferenceRepository($this->referenceRepo);
        $equipe = $teamFixture->load($this->entityManager);

        $this->client->request('GET', '/tousLesMatchesPourEquipe/' . $equipe->getTeamId());

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('Tous les matchs',$this->client->getResponse());
    }
}