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

    /**
     * @test
     */
    public function le_detail_classement_est_genere()
    {
        $this->client->request('GET', '/classement/detail/6');

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('Equipe',$this->client->getResponse());
    }

    /**
     * @test
     */
    public function les_sous_classement_equipe_s_affichent_bien()
    {
        $this->client->request('GET', '/classementEquipe/td/5/6');

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('Le plus de TD',$this->client->getResponse());
    }

    /**
     * @test
     */
    public function les_sous_classement_joueur_s_affichent_bien()
    {
        $this->client->request('GET', '/classementJoueur/td/5/6');

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('Le Marqueur - Record TD',$this->client->getResponse());
    }

    /**
     * @test
     */
    public function le_total_des_cas_s_affiche()
    {
        $this->client->request('GET', '/totalcas/6');

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('<strong>Total : 0 En 0 Matches.</strong>',$this->client->getResponse());
    }

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