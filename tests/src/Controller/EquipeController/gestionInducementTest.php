<?php


namespace App\Tests\src\Controller\EquipeController;


use App\DataFixtures\TeamFixture;
use App\Tests\src\Functionnal;

class gestionInducementTest extends Functionnal
{
    /**
     * @test
     */
    public function la_reponse_est_un_json()
    {
        $equipeFixture = new TeamFixture();
        $equipeFixture->setReferenceRepository($this->referenceRepo);
        $equipeTest = $equipeFixture->load($this->entityManager);

        $this->client->request('GET', '/gestionInducement/add/' . $equipeTest->getTeamId() . '/rr' );

        $this->assertResponseIsSuccessful();
        $this->assertJson( $this->client->getResponse()->getContent());
    }
}