<?php

namespace App\Controller;

use App\Entity\Defis;
use App\Entity\Teams;
use App\Form\AjoutDefisType;
use App\Service\DefisService;
use App\Service\SettingsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefisIhmController extends AbstractController
{
    /**
     * @Route("/ajoutDefisForm/{coachId}", name="ajoutDefisForm")
     * @param int $coachId
     * @return Response
     */
    public function ajoutDefisForm(int $coachId): \Symfony\Component\HttpFoundation\Response
    {
        $defis = new Defis();

        $form = $this->createForm(AjoutDefisType::class, $defis, ['coach' => $coachId]);

        return $this->render('statbb/addDefis.html.twig', ['form' => $form->createView(), 'coachId' => $coachId]);
    }

    /**
     * @Route("/ajoutDefis", name="ajoutDefis")
     * @param Request $request
     * @param DefisService $defisService
     * @param SettingsService $settingService
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function ajoutDefis(
        Request $request,
        \App\Service\DefisService $defisService,
        SettingsService $settingService
    ) : \Symfony\Component\HttpFoundation\RedirectResponse {
        $datas = $request->request->get('ajout_defis');

        /** @var Teams $equipe */
        $equipe = $this->getDoctrine()->getRepository(Teams::class)->findOneBy(['teamId' => $datas['equipeOrigine']]);

        if (!empty($equipe)) {
            if ($defisService->defiAutorise(
                $equipe,
                $settingService
            )) {
                $defisService->creerDefis($datas);
                $this->addFlash('success', 'Défis Ajouté!');
            } else {
                $this->addFlash('fail', 'Plus de défis pour cette période');
            }
        }

        return $this->redirectToRoute('frontUser');
    }

    /**
     * @Route("/afficherDefis", name="afficherDefis", options = { "expose" = true })
     * @param SettingsService $settingsService
     * @return Response
     */
    public function afficherLesDefis(SettingsService $settingsService): \Symfony\Component\HttpFoundation\Response
    {
        return $this->render(
            'statbb/tabs/ligue/affichageDefis.html.twig',
            [
                'defisCollection' => $this->getDoctrine()->getRepository(Defis::class)->listeDefisEnCours(
                    $settingsService->anneeCourante()
                ),
            ]
        );
    }

    /**
     * @Route("/afficherPeriodeDefisActuelle", name="afficherPeriodeDefisActuelle")
     * @param SettingsService $settingsService
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function afficherPeriodeDefisActuelle(SettingsService $settingsService)
    : \Symfony\Component\HttpFoundation\Response
    {
        $periode = $settingsService->periodeDefisCourrante();

        if ($periode['debut'] != false && $periode['fin'] != false) {
            return new Response($periode['debut']->format('d/m/Y') . ' - ' . $periode['fin']->format('d/m/Y'));
        }

        return new Response('Error');
    }

    /**
     * @Route("/supprimerDefis/{defisId}", name="supprimerDefis")
     * @param DefisService $defisService
     * @param int $defisId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function supprimerPrime(DefisService $defisService, int $defisId)
    : \Symfony\Component\HttpFoundation\RedirectResponse
    {
        if ($defisService->supprimerDefis($defisId) !== '') {
            $this->addFlash('success', 'Defis Supprimée');
        }

        return $this->redirectToRoute('frontUser');
    }
}
