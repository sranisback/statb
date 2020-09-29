<?php


namespace App\Tests\src\Controller\ClassementController;


use App\Tests\src\Functionnal;

class classGenDetailTest extends Functionnal
{
    /**
     * @test
     */
    public function le_detail_classement_est_genere()
    {
        $this->client->request('GET', '/classement/detail/6');

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('Equipe',$this->client->getResponse());
    }
}