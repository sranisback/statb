<?php


namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class CoachService
{
    public function __construct(EntityManagerInterface $doctrineEntityManager)
    {
    }
}
