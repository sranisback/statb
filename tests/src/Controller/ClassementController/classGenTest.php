<?php


namespace App\Tests\src\Controller\ClassementController;


use App\Tests\src\Functionnal;

class classGenTest extends Functionnal
{
    /**
     * @test
     */
    public function le_classement_est_genere()
    {
        $this->client->request('GET', '/classement/general/6');

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('Classement',$this->client->getResponse());
    }
}