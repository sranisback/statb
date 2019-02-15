<?php

namespace App\Service;

use App\Entity\Setting;

use Doctrine\Common\Persistence\ManagerRegistry;

class settingsService
{
    private $doctrineEntityManager;

    public function __construct(ManagerRegistry $doctrineEntityManager)
    {
        $this->doctrineEntityManager = $doctrineEntityManager;
    }

    /**
     * @return int $anneeCourante
     */
    public function anneeCourante()
    {

        return $this->doctrineEntityManager->getRepository(Setting::class)->findOneBy(['name' => 'year'])->getValue();
    }
}
