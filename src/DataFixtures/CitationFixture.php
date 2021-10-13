<?php

namespace App\DataFixtures;

use App\Entity\Citations;
use App\Entity\Coaches;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CitationFixture extends Fixture
{
    /**
     * @var Citations
     */
    private $citationFixture;

    /**
     * @param ObjectManager $manager
     * @return Citations
     */
    public function load(ObjectManager $manager): Citations
    {
        $this->citationFixture = new Citations();
        $this->citationFixture->setCitation('test !');
        /** @var Coaches $coach */
        $coach = $this->getReference(CoachesFixture::COACH_FIXTURE);
        $this->citationFixture->setCoachId($coach);

        $manager->persist($this->citationFixture);
        $manager->flush();

        return $this->citationFixture;
    }

    public function deleteFixture(ObjectManager $manager) : void
    {
        $manager->remove($this->citationFixture);
        $manager->flush();
    }
}
