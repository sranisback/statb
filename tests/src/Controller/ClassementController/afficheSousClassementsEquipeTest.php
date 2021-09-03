<?php


namespace App\Tests\src\Controller\ClassementController;


use App\DataFixtures\SettingFixtures;
use App\Tests\src\Functionnal;

class afficheSousClassementsEquipeTest extends Functionnal
{
    /**
     * @test
     */
    public function les_sous_classement_equipe_s_affichent_bien()
    {
        $settingFixture = new SettingFixtures();
        $settingTest = $settingFixture->load($this->entityManager);
        $settingTest->setName('currentRuleset');
        $settingTest->setValue('0');

        $this->entityManager->persist($settingTest);
        $this->entityManager->flush();

        $settingFixture = new SettingFixtures();
        $settingTest = $settingFixture->load($this->entityManager);
        $settingTest->setName('year');
        $settingTest->setValue('1');

        $this->entityManager->persist($settingTest);
        $this->entityManager->flush();

        $this->client->request('GET', '/classementEquipe/td/5/6');

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('Le plus de TD',$this->client->getResponse());
    }
}