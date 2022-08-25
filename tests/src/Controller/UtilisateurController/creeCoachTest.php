<?php


namespace App\Tests\src\Controller\UtilisateurController;


use App\Tests\src\Functionnal;

class creeCoachTest extends Functionnal
{
    /**
     * @test
     */
    public function le_coach_se_cree()
    {
        $this->client->request('GET', '/creeCoach');

        $this->assertResponseIsSuccessful();
    }
}