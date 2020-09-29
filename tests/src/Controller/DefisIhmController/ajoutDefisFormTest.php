<?php


namespace App\Tests\src\Controller\DefisIhmController;

use App\DataFixtures\CoachesFixture;
use App\Tests\src\Functionnal;

class ajoutDefisFormTest extends Functionnal
{
    /**
     * @test
     */
    public function le_form_est_bien_affiche()
    {
        $coachFixture = new CoachesFixture();
        $coachFixture->setReferenceRepository($this->referenceRepo);
        $coach = $coachFixture->load($this->entityManager);

        $this->client->loginUser($coach);
        $this->client->followRedirects(true);

        $this->client->request('GET', '/ajoutDefisForm');

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('Creer un Défis', $this->client->getResponse());
    }
}
