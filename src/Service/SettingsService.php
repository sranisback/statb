<?php

namespace App\Service;

use App\Entity\Setting;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;

class SettingsService
{
    private $doctrineEntityManager;

    public function __construct(EntityManagerInterface $doctrineEntityManager)
    {
        $this->doctrineEntityManager = $doctrineEntityManager;
    }

    /**
     * @return int $anneeCourante
     */
    public function anneeCourante()
    {
        try {
            return $this->doctrineEntityManager->getRepository(Setting::class)->findOneBy(['name' => 'year'])->getValue(
            );
        } catch (ORMException $e) {
        }
        return 0;
    }
}
