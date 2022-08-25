<?php


namespace App\Service;

use App\Entity\Penalite;
use App\Entity\Teams;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Utils\DateTime;

class PenaliteService
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $doctrineEntityManager;

    public function __construct(EntityManagerInterface $doctrineEntityManager)
    {
        $this->doctrineEntityManager = $doctrineEntityManager;
    }

    /**
     * @param Penalite $penalite
     * @param $form
     * @return bool
     */
    public function creerUnePenalite(Penalite $penalite, $form): bool
    {
        if ($form->isSubmitted() && $form->isValid()) {
            $penalite->setDate(DateTime::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s")));

            $this->doctrineEntityManager->persist($penalite);
            $this->doctrineEntityManager->flush();

            return true;
        }

        return false;
    }

}
