<?php

namespace App\DataFixtures;

use App\Entity\Setting;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SettingFixtures extends Fixture
{
    private $settingFixture;

    public function load(ObjectManager $manager)
    {
        $this->settingFixture = new Setting();
        $this->settingFixture->setValue('6');
        $this->settingFixture->setName('Year');

        $manager->persist($this->settingFixture);
        $manager->flush();
    }

    public function deleteFixture(ObjectManager $manager)
    {
        $manager->remove($this->settingFixture);
        $manager->flush();
    }
}
