<?php


namespace App\Tests\src\Controller\ClassementController;


use App\Tests\src\Functionnal;

class cinqDernierMatchTest extends Functionnal
{
    /**
     * @test
     */
    public function les_cinq_dernier_matchs()
    {
        $this->client->request('GET', '/cinqDernierMatch');

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('Les derniers matchs',$this->client->getResponse());
    }
}