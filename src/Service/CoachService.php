<?php


namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class CoachService
{
    private $doctrineEntityManager;

    public function __construct(EntityManagerInterface $doctrineEntityManager)
    {
        $this->doctrineEntityManager = $doctrineEntityManager;
    }
}
