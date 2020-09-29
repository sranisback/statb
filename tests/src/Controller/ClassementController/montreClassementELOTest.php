<?php


namespace App\Tests\src\Controller\ClassementController;


use App\Tests\src\Functionnal;

class montreClassementELOTest extends Functionnal
{
    /**
     * @test
     */
    public function monte_le_classement_elo()
    {
        $this->client->request('GET', '/montreClassementELO');

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('TableElo',$this->client->getResponse());
    }
}