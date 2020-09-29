<?php


namespace App\Tests\src\Controller\EquipeController;


use App\Tests\src\Functionnal;

class createTeamTest extends Functionnal
{
    /**
     * @test
     */
    public function le_form_s_affiche_bien()
    {
        $this->client->request('GET', '/createTeam' );

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('Créer une équipe', $this->client->getResponse());
    }
}