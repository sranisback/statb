<?php


namespace App\Tests\src\Controller\ClassementController;


use App\Tests\src\Functionnal;

class montreLeCimetiereTest extends Functionnal
{
    /**
     * @test
     */
    public function monte_le_cimetierre()
    {
        $this->client->request('GET', '/montreLeCimetierre');

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('TableCimetierre',$this->client->getResponse());
    }
}