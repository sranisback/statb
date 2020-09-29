<?php


namespace App\Tests\src\Controller\ClassementController;


use App\DataFixtures\SettingFixtures;
use App\Tests\src\Functionnal;

class calculClassementGenTest extends Functionnal
{
    /**
     * @test
     */
    public function calcul_classement_controlleur()
    {
        $settingFixture = new SettingFixtures();
        $settingTest = $settingFixture->load($this->entityManager);

        $settingTest->setName('points_6');
        $this->entityManager->persist($settingTest);
        $this->entityManager->flush();

        $this->client->request('GET', '/calculClassementGen/6');

        $this->assertResponseRedirects('/');
    }
}