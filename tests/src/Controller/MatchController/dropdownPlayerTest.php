<?php


namespace App\Tests\src\Controller\MatchController;


use App\DataFixtures\TeamFixture;
use App\Tests\src\Functionnal;

class dropdownPlayerTest extends Functionnal
{
    /**
     * @test
     */
    public function dropdown_retour_json()
    {
        $equipeFixture = new TeamFixture();
        $equipeFixture->setReferenceRepository($this->referenceRepo);
        $equipeTest = $equipeFixture->load($this->entityManager);

        $this->client->request('GET', '/dropdownPlayer/' . $equipeTest->getTeamId() . '/1'  );

        $this->assertResponseIsSuccessful();
    }
}