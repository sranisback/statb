<?php

namespace App\DataFixtures;

use App\Entity\Citations;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CitationFixture extends Fixture
{
    private $citationFixture;

    public function load(ObjectManager $manager)
    {
        $this->citationFixture = new Citations();
        $this->citationFixture->setCitation('test !');
        $this->citationFixture->setCoachId($this->getReference(CoachesFixture::COACH_FIXTURE));

        $manager->persist($this->citationFixture);
        $manager->flush();

        return $this->citationFixture;
    }

    public function deleteFixture(ObjectManager $manager)
    {
        $manager->remove($this->citationFixture);
        $manager->flush();
    }
}
