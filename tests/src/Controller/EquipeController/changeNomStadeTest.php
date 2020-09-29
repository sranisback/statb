<?php


namespace App\Tests\src\Controller\EquipeController;

use App\DataFixtures\StadesFixture;
use App\Tests\src\Functionnal;

class changeNomStadeTest extends Functionnal
{
    /**
     * @test
     */
    public function le_controlleur_repond_bien()
    {
        $stadeFixture = new StadesFixture();
        $stadeTest = $stadeFixture->load($this->entityManager);

        $this->client->request('POST', '/changeNomStade',
            [
                'pk' => $stadeTest->getId(),
                'name' => 'Nom',
                'value' => 'Zob'
            ]
        );

        $this->assertResponseIsSuccessful();
    }
}