<?php


namespace App\Tests\src\Controller\ClassementController;

use App\DataFixtures\CoachesFixture;
use App\Tests\src\Functionnal;

class afficheConfrontationTest extends Functionnal
{
    /**
     * @test
     */
    public function monte_le_classement_elo()
    {
        $coachFixture = new CoachesFixture();
        $coachFixture->setReferenceRepository($this->referenceRepo);
        $coach = $coachFixture->load($this->entityManager);

        $this->client->loginUser($coach);

        $this->client->request('GET', '/montreConfrontation');

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('tableConfrontations', $this->client->getResponse());
    }
}
