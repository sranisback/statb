<?php


namespace App\Service;


use App\Entity\Penalite;
use App\Entity\Teams;
use Doctrine\ORM\EntityManagerInterface;

class PenaliteService
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private \Doctrine\ORM\EntityManagerInterface $doctrineEntityManager;

    public function __construct(EntityManagerInterface $doctrineEntityManager)
    {
        $this->doctrineEntityManager = $doctrineEntityManager;
    }

    public function creerUnePenalite(array $datas)
    {
        $penalite = new Penalite();
        $penalite->setEquipe($this->doctrineEntityManager->getRepository(Teams::class)->findOneBy(
            ['teamId' => $datas['equipe']]
        ));
        $penalite->setPoints($datas['points']);

        $this->doctrineEntityManager->persist($penalite);
        $this->doctrineEntityManager->flush();

        return $penalite;
    }

}