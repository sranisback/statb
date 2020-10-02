<?php


namespace App\Tests\src\Controller\MatchController;


use App\Tests\src\Functionnal;

class matchsAnneeTest extends Functionnal
{
    /**
     * @test
     */
    public function matchs_annee()
    {
        $this->client->request('GET', '/matchsAnnee');

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('listeMatchs', $this->client->getResponse());
    }
}