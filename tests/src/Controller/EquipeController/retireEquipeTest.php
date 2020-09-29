<?php


namespace App\Tests\src\Controller\EquipeController;

use App\DataFixtures\TeamFixture;
use App\Tests\src\Functionnal;

class retireEquipeTest extends Functionnal
{
    /**
     * @test
     */
    public function retire_equipe()
    {
        $equipeFixture = new TeamFixture();
        $equipeFixture->setReferenceRepository($this->referenceRepo);
        $equipeTest = $equipeFixture->load($this->entityManager);

        $this->client->request('GET', '/retireEquipe/' . $equipeTest->getTeamId());

        $this->assertResponseStatusCodeSame(302);
    }
}
