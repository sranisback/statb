<?php


namespace App\Tests\src\Controller\ClassementController;


use App\Tests\src\Functionnal;

class affichetotalCasTest extends Functionnal
{
    /**
     * @test
     */
    public function le_total_des_cas_s_affiche()
    {
        $this->client->request('GET', '/totalcas/6');

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('<strong>Total : 0 En 0 Matches.</strong>',$this->client->getResponse());
    }

}