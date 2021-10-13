<?php

namespace App\DataFixtures;

use App\Entity\Setting;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SettingFixtures extends Fixture
{
    /** @var Setting $settingFixture */
    private $settingFixture;

    public function load(ObjectManager $manager) : Setting
    {
        $this->settingFixture = new Setting();
        $this->settingFixture->setValue('6');
        $this->settingFixture->setName('Year');

        $manager->persist($this->settingFixture);
        $manager->flush();

        return $this->settingFixture;
    }

    public function deleteFixture(ObjectManager $manager) : void
    {
        $manager->remove($this->settingFixture);
        $manager->flush();
    }
}
