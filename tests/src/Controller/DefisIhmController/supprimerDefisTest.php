<?php


namespace App\Tests\src\Controller\DefisIhmController;

use App\DataFixtures\DefisFixture;
use App\Tests\src\Functionnal;

class supprimerDefisTest extends Functionnal
{
    /**
     * @test
     */
    public function la_page_est_bien_redirigee()
    {
        $defisFixture = new DefisFixture();
        $defisTest = $defisFixture->load($this->entityManager);

        $this->client->request('GET', '/supprimerDefis/'. $defisTest->getId());
        $this->client->followRedirects(true);

        $this->assertResponseRedirects('/frontUser');
    }
}
