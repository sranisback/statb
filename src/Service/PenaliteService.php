<?php


namespace App\Service;

use App\Entity\Penalite;
use App\Entity\Teams;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Utils\DateTime;

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

    /**
     * @param array<mixed> $datas
     * @return Penalite
     */
    public function creerUnePenalite(array $datas): Penalite
    {
        $penalite = new Penalite();
        $penalite->setEquipe($this->doctrineEntityManager->getRepository(Teams::class)->findOneBy(
            ['teamId' => $datas['equipe']]
        ));
        $penalite->setPoints($datas['points']);
        $penalite->setMotif($datas['motif']);
        $penalite->setDate(DateTime::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s")));

        $this->doctrineEntityManager->persist($penalite);
        $this->doctrineEntityManager->flush();

        return $penalite;
    }
}
