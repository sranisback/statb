<?php


namespace App\Service;


use App\Entity\Sponsors;
use App\Entity\Teams;
use Doctrine\ORM\EntityManagerInterface;

class SponsorService
{
    private EntityManagerInterface $doctrineEntityManager;

    private InfosService $infosService;

    public function __construct(EntityManagerInterface $doctrineEntityManager, InfosService $infosService)
    {
        $this->doctrineEntityManager = $doctrineEntityManager;
        $this->infosService = $infosService;
    }

    public function tireSponsorAuHasard()
    {
        $sponsors = $this->doctrineEntityManager->getRepository(Sponsors::class)->findAll();

        if ($sponsors) {
            $nbrAuHasard = rand(0, count($sponsors) - 1);

            return $sponsors[$nbrAuHasard];
        }

        return new Sponsors();
    }

    public function affecteUnSponsor(Teams $equipe)
    {
        $sponsor = $this->tireSponsorAuHasard();

        $equipe->setSponsor($sponsor);

        $this->doctrineEntityManager->persist($equipe);
        $this->doctrineEntityManager->flush();
        $this->doctrineEntityManager->refresh($equipe);

        $this->infosService->sponsorAjoute($equipe);
    }

    public function supprimeUnSponsor(Teams $equipe)
    {
        $this->infosService->sponsorSupprime($equipe);

        $equipe->setSponsor(null);

        $this->doctrineEntityManager->persist($equipe);
        $this->doctrineEntityManager->flush();
        $this->doctrineEntityManager->refresh($equipe);
    }
}