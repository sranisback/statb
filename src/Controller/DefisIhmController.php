<?php

namespace App\Controller;

use App\Entity\Defis;
use App\Entity\Teams;
use App\Form\AjoutDefisType;
use App\Service\DefisService;
use App\Service\SettingsService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefisIhmController extends AbstractController
{
    private ManagerRegistry $doctrine;

    private SettingsService $settingsService;

    private DefisService $defisService;

    public function __construct(ManagerRegistry $doctrine, SettingsService $settingsService, DefisService $defisService)
    {
        $this->doctrine = $doctrine;
        $this->settingsService = $settingsService;
        $this->defisService = $defisService;
    }

    /**
     * @Route("/ajoutDefisForm/", name="ajoutDefisForm")
     * @param Request $request
     * @param DefisService $defisService
     * @return Response
     */
    public function ajoutDefisForm(Request $request): Response {
        $defis = new Defis();

        $form = $this->createForm(AjoutDefisType::class, $defis);
        $form->handleRequest($request);

        $defiDispo = $this->defisService->ajoutDefiForm($form, $defis);

        if($defiDispo == 1) {
            $this->addFlash('success', 'Défis Ajouté!');

            return $this->redirectToRoute('frontUser');
        } else if ($defiDispo == 2) {
            $this->addFlash('fail', 'Plus de défis pour cette période');
        }

        return $this->render(
            'statbb/addDefis.html.twig',
            [
                'form' => $form->createView(),
                'defis' => $defis
            ]
        );
    }

    /**
     * @Route("/afficherDefis", name="afficherDefis", options = { "expose" = true })
     * @return Response
     */
    public function afficherLesDefis(): Response
    {
        return $this->render(
            'statbb/tabs/ligue/affichageDefis.html.twig',
            [
                'defisCollection' => $this->doctrine->getRepository(Defis::class)->listeDefisEnCours(
                    $this->settingsService->anneeCourante()
                ),
            ]
        );
    }

    /**
     * @Route("/afficherPeriodeDefisActuelle", name="afficherPeriodeDefisActuelle")
     * @return Response
     */
    public function afficherPeriodeDefisActuelle() : Response
    {
        $periode = $this->settingsService->periodeDefisCourrante();

        if (!empty($periode) && ($periode['debut'] && $periode['fin'])) {
            return new Response($periode['debut']->format('d/m/Y') . ' - ' . $periode['fin']->format('d/m/Y'));
        }

        return new Response('Periode defis pas configurée/abscente !');
    }

    /**
     * @Route("/supprimerDefis/{defisId}", name="supprimerDefis")
     * @param DefisService $defisService
     * @param int $defisId
     * @return RedirectResponse
     */
    public function supprimerDefis(int $defisId) : RedirectResponse
    {
        if ($this->defisService->supprimerDefis($defisId) !== '') {
            $this->addFlash('success', 'Defis Supprimée');
        }

        return $this->redirectToRoute('frontUser');
    }
}
