<?php


namespace App\Tests\src\Controller\MatchController;

use App\Tests\src\Functionnal;

class ajoutMatchTest extends Functionnal
{
    /**
     * @test
     */
    public function affichage_form_matchs()
    {
        $this->client->request('GET', '/ajoutMatch');

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('Ajouter un match', $this->client->getResponse());
    }
}
