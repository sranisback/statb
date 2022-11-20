<?php

namespace App\Controller;

use App\Entity\Teams;
use App\Service\SponsorService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class SponsorController extends AbstractController
{

    private SponsorService $sponsorService;

    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $doctrine, SponsorService $sponsorService)
    {
        $this->doctrine = $doctrine;
        $this->sponsorService = $sponsorService;
    }

    /**
     * @Route ("/addSponso/{equipeId}", name="addSponso")
     */
    public function addSponso(int $equipeId): RedirectResponse
    {
        /** @var Teams $equipe */
        $equipe = $this->doctrine->getRepository(Teams::class)->findOneBy(['teamId' => $equipeId]);

        $this->sponsorService->affecteUnSponsor($equipe);

        return $this->redirectToRoute('team', ['teamid' => $equipeId]);
    }

    /**
     * @Route ("/supprSponso/{equipeId}", name="supprSponso")
     */
    public function supprSponso(int $equipeId): RedirectResponse
    {
        /** @var Teams $equipe */
        $equipe = $this->doctrine->getRepository(Teams::class)->findOneBy(['teamId' => $equipeId]);

        $this->sponsorService->supprimeUnSponsor($equipe);

        return $this->redirectToRoute('team', ['teamid' => $equipeId]);
    }
}
