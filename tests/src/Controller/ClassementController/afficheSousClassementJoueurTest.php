<?php


namespace App\Tests\src\Controller\ClassementController;


use App\Tests\src\Functionnal;

class afficheSousClassementJoueurTest extends Functionnal
{
    /**
     * @test
     */
    public function les_sous_classement_joueur_s_affichent_bien()
    {
        $this->client->request('GET', '/classementJoueur/td/5/6');

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('Le Marqueur - Record TD',$this->client->getResponse());
    }

}