<?php


namespace App\Tests\src\Controller\EquipeController;


use App\DataFixtures\TeamFixture;
use App\Tests\src\Functionnal;

class ajoutStadeModalTest extends Functionnal
{
    /**
     * @test
     */
    public function le_form_est_affiche()
    {
        $equipeFixture = new TeamFixture();
        $equipeFixture->setReferenceRepository($this->referenceRepo);
        $equipeTest = $equipeFixture->load($this->entityManager);

        $this->client->request('GET', '/ajoutStadeModal/' . $equipeTest->getTeamId() );

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('Acheter un stade', $this->client->getResponse());
    }

}