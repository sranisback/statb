<?php


namespace App\Tests\src\Controller\EquipeController;


use App\DataFixtures\TeamFixture;
use App\Tests\src\Functionnal;

class listeDesJoueursTest extends Functionnal
{
    /**
     * @test
     */
    public function liste_des_joueurs()
    {
        $equipeFixture = new TeamFixture();
        $equipeFixture->setReferenceRepository($this->referenceRepo);
        $equipeTest = $equipeFixture->load($this->entityManager);

        $this->client->request('GET', '/listeDesJoueurs/' . $equipeTest->getTeamId() );

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('liste_joueur_adder', $this->client->getResponse());
    }
}