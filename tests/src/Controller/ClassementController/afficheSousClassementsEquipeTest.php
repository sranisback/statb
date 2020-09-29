<?php


namespace App\Tests\src\Controller\ClassementController;


use App\Tests\src\Functionnal;

class afficheSousClassementsEquipeTest extends Functionnal
{
    /**
     * @test
     */
    public function les_sous_classement_equipe_s_affichent_bien()
    {
        $this->client->request('GET', '/classementEquipe/td/5/6');

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('Le plus de TD',$this->client->getResponse());
    }
}