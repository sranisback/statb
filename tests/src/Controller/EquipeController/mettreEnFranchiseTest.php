<?php


namespace App\Tests\src\Controller\EquipeController;


use App\DataFixtures\TeamFixture;
use App\Tests\src\Functionnal;

class mettreEnFranchiseTest extends Functionnal
{
    /**
     * @test
     */
    public function mettre_en_franchise()
    {
        $teamFixture = new TeamFixture();
        $teamFixture->setReferenceRepository($this->referenceRepo);
        $equipeTest = $teamFixture->load($this->entityManager);

        $this->client->request('GET', '/mettreEnFranchise/' . $equipeTest->getTeamId());

        $this->assertResponseStatusCodeSame(302);
    }
}