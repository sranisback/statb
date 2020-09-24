<?php


namespace App\Tests\src;


use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class Functionnal extends WebTestCase
{
    public $client;
    public $entityManager;
    public $referenceRepo;

    public function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
        $container = $this->client->getContainer();
        $doctrine = $container->get('doctrine');
        $this->entityManager = $doctrine->getManager();

        $purger = new ORMPurger($this->entityManager);
        $purger->setPurgeMode(1);
        $purger->purge();

        $this->referenceRepo = new ReferenceRepository($this->entityManager);
    }

    public function tearDown(): void
    {
        parent::tearDown();

        $purger = new ORMPurger($this->entityManager);
        $purger->setPurgeMode(1);
        $purger->purge();
    }
}