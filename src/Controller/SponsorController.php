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
     * @Route ("/addSponso/{teamId}", name="addSponso")
     */
    public function addSponso(Teams $equipe): RedirectResponse
    {
        $this->sponsorService->affecteUnSponsor($equipe);

        return $this->redirectToRoute('team', ['teamid' => $equipe->getTeamId()]);
    }

    /**
     * @Route ("/supprSponso/{teamId}", name="supprSponso")
     */
    public function supprSponso(Teams $equipe): RedirectResponse
    {
        $this->sponsorService->supprimeUnSponsor($equipe);

        return $this->redirectToRoute('team', ['teamid' => $equipe->getTeamId()]);
    }
}
