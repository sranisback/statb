<?php


namespace App\Tests\src\Controller\EquipeController;


use App\DataFixtures\TeamFixture;
use App\Tests\src\Functionnal;

class supprimeLogoTest extends Functionnal
{
    /**
     * @test
     */
    public function le_controller_repond()
    {
        $equipeFixture = new TeamFixture();
        $equipeFixture->setReferenceRepository($this->referenceRepo);
        $equipeTest = $equipeFixture->load($this->entityManager);

        $this->client->request('GET', '/supprimeLogo/' . $equipeTest->getTeamId());

        $this->assertResponseIsSuccessful();
    }
}