<?php


namespace App\Tests\src\Controller\EquipeController;

use App\DataFixtures\CoachesFixture;
use App\Tests\src\Functionnal;

class showuserteamsTest extends Functionnal
{
    /**
     * @test
     */
    public function equipes_montrees()
    {
        $coachFixture = new CoachesFixture();
        $coachFixture->setReferenceRepository($this->referenceRepo);
        $this->client->loginUser($coachFixture->load($this->entityManager));
        
        $this->client->request('GET', '/showuserteams');

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('equipesEnCours', $this->client->getResponse());
    }
}
