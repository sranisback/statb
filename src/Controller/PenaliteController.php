<?php


namespace App\Controller;

use App\Entity\Penalite;
use App\Entity\Teams;
use App\Form\AjoutPenaliteForm;
use App\Service\PenaliteService;
use App\Service\SettingsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PenaliteController extends AbstractController
{
    /**
     * @Route("/ajoutPenaliteForm", name="ajoutPenaliteForm")
     * @return Response
     */
    public function ajoutPenaliteForm(): \Symfony\Component\HttpFoundation\Response
    {
        $penalite = new Penalite();

        $form = $this->createForm(AjoutPenaliteForm::class, $penalite);

        return $this->render('statbb/addPenalite.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/ajoutPenalite", name="ajoutPenalite")
     * @param Request $request
     * @param PenaliteService $penaliteService
     * @param SettingsService $settingService
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function ajoutPenalite(Request $request, PenaliteService $penaliteService, SettingsService $settingService)
    : \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $datas = $request->request->get('ajout_penalite_form');

        /** @var Teams $equipe */
        $equipe = $this->getDoctrine()->getRepository(Teams::class)->findOneBy(['teamId' => $datas['equipe']]);

        if (!empty($equipe)) {
            $penaliteService->creerUnePenalite($datas);
            $this->addFlash('success', 'Penalité Ajouté!');
        }

        return $this->redirectToRoute('frontUser');
    }

    /**
     * @Route("/afficherPenalite", name="afficherPenalite")
     * @param SettingsService $settingsService
     * @return Response
     */
    public function afficherPenalite(SettingsService $settingsService): \Symfony\Component\HttpFoundation\Response
    {
        return $this->render(
            'statbb/tabs/ligue/affichagePenalite.html.twig',
            [
                'penaliteCollection' => $this->getDoctrine()->getRepository(Penalite::class)->listePenaliteEnCours(
                    $settingsService->anneeCourante()
                ),
            ]
        );
    }
}
