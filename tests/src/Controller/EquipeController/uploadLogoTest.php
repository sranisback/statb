<?php


namespace App\Tests\src\Controller\EquipeController;

use App\DataFixtures\TeamFixture;
use App\Tests\src\Functionnal;

class uploadLogoTest extends Functionnal
{
    /**
     * @Test
     */
    public function test_upload_logo()
    {
        $equipeFixture = new TeamFixture();
        $equipeFixture->setReferenceRepository($this->referenceRepo);
        $equipeTest = $equipeFixture->load($this->entityManager);

        $this->client->request('GET', '/uploadLogo/' . $equipeTest->getTeamId());

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('Ajouter un logo', $this->client->getResponse());
    }
}
