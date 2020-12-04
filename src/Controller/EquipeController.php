<?php

namespace App\Controller;

use App\Entity\Coaches;
use App\Entity\Players;
use App\Entity\Stades;
use App\Entity\Teams;
use App\Enum\AnneeEnum;
use App\Enum\NiveauStadeEnum;
use App\Form\CreerStadeType;
use App\Form\LogoEnvoiType;
use App\Service\AdminService;
use App\Service\EquipeService;
use App\Service\PlayerService;
use App\Service\SettingsService;
use App\Form\CreerEquipeType;

use App\Service\StadeService;
use App\Tools\TransformeEnJSON;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Security;

class EquipeController extends AbstractController
{
    /**
     * @Route("/montreLesEquipes", name="showteams")
     * @param SettingsService $settingsService
     * @return response
     */
    public function montreLesEquipes(SettingsService $settingsService): \Symfony\Component\HttpFoundation\Response
    {
        return $this->render(
            'statbb/tabs/ligue/showteams.html.twig',
            [
                'teams' => $this->getDoctrine()->getRepository(Teams::class)->findBy(
                    ['year' => $settingsService->anneeCourante()]
                ),
            ]
        );
    }

    /**
     * @Route("/montreLesAnciennesEquipes", name="showOldTeams")
     * @param EquipeService $equipeService
     * @param Security $security
     * @return response
     */
    public function montreLesAnciennesEquipes(
        EquipeService $equipeService
    ): \Symfony\Component\HttpFoundation\Response {
        return $this->render(
            'statbb/tabs/coach/anciennesEquipes.html.twig',
            ['listeEquipe' => $equipeService->compileLesEquipes($this->getUser())]
        );
    }

    /**
     * @Route("/showuserteams", name="showuserteams")
     * @param SettingsService $settingsService
     * @param EquipeService $equipeService
     * @return response
     */
    public function showUserTeams(
        EquipeService $equipeService
    ): \Symfony\Component\HttpFoundation\Response {
        return $this->render(
            'statbb/tabs/coach/user_teams.html.twig',
            ['listeEquipe' => $equipeService->compileEquipesAnneeEnCours($this->getUser())]
        );
    }

    /**
     * @Route("/team/{teamid}", name="team", requirements={"teamid"="\d+"}, options = { "expose" = true })
     * @param PlayerService $playerService
     * @param EquipeService $equipeService
     * @param int $teamid
     * @return Response
     */
    public function showTeam(
        PlayerService $playerService,
        EquipeService $equipeService,
        int $teamid
    ): \Symfony\Component\HttpFoundation\Response {
        /** @var Teams $equipe */
        $equipe = $this->getDoctrine()->getRepository(Teams::class)->findOneBy(['teamId' => $teamid]);

        return $this->render(
            'statbb/team.html.twig',
            [
                'feuille' => $equipeService->feuilleDequipeComplete($equipe, $playerService),
                'niveauStade' => NiveauStadeEnum::numeroVersNiveauDeStade()
            ]
        );
    }

    /**
     * @Route("/team/{nomEquipe}", name="montreEquipe",
     *     requirements={"nommEquipe" = "\D+"}, options = { "expose" = true })
     * @param string $nomEquipe
     * @return Response
     */
    public function montreEquipe(
        string $nomEquipe,
        SettingsService $settingsService
    ): \Symfony\Component\HttpFoundation\Response {
        if ($nomEquipe !== '') {
            /** @var Teams[] $equipe */
            $equipes = $this->getDoctrine()->getRepository(Teams::class)->requeteEquipeLike($nomEquipe);

            if (count($equipes) > 1) {
                return $this->render(
                    'statbb/didYouMean.html.twig',
                    [
                        'listeEquipe' => $equipes,
                        'annees' => (new AnneeEnum)->numeroToAnnee()
                    ]
                );
            }
            if ($equipes !== []) {
                return $this->redirectToRoute('team', ['teamid' => $equipes[0]->getTeamId()]);
            }
        }
        return $this->render('statbb/front.html.twig', ['annee' => $settingsService->anneeCourante()]);
    }

    /**
     * @Route("/uploadLogo/{equipeId}", name="uploadLogo")
     * @param Request $request
     * @param int $equipeId
     */
    public function uploadLogo(Request $request, EquipeService $equipeService, int $equipeId)
    {
        $equipe = $this->getDoctrine()->getRepository(Teams::class)->findOneBy(['teamId' => $equipeId]);

        $form = $this->createForm(LogoEnvoiType::class, $equipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $equipeService->enregistreLogo($request, $this->getParameter('logo_directory'), $equipe);

            return $this->redirectToRoute('team', ['teamid' => $equipe->getTeamId()]);
        }

        return $this->render(
            'statbb/addLogo.html.twig',
            [
                'form' => $form->createView(),
                'team' => $equipe
            ]
        );
    }

    /**
     * @Route("/createTeam", name="createTeam")
     * @param Request $request
     * @param EquipeService $equipeService
     */
    public function createTeam(
        Request $request,
        EquipeService $equipeService
    ) {
        $equipe = new Teams();

        $form = $this->createForm(CreerEquipeType::class, $equipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $datas = $request->request->get('creer_equipe');

            /** @var Coaches $coach */
            $teamid = $equipeService->createTeam($datas['Name'], $this->getUser()->getCoachId(), $datas['fRace']);

            if ($teamid !== 0) {
                $this->addFlash('success', 'Equipe Ajoutée!');
            }

            return $this->redirectToRoute('team', ['teamid' => $teamid]);
        }

        return $this->render('statbb/addteam.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/retireEquipe/{teamId}", name="retireEquipe")
     * @param int $teamId
     */
    public function retireEquipe(int $teamId)
    {
        $entityManager = $this->getDoctrine()->getManager();

        /** @var Teams $team */
        $equipe = $this->getDoctrine()->getRepository(Teams::class)->findOneBy(['teamId' => $teamId]);
        $equipe->setRetired(true);

        $entityManager->persist($equipe);
        $entityManager->flush();

        return $this->redirectToRoute('frontUser');
    }

    /**
     * @Route("/gestionInducement/{action}/{teamId}/{type}", name="gestionInducement", options = { "expose" = true })
     * @param EquipeService $equipeService
     * @param PlayerService $playerService
     * @param string $action
     * @param int $teamId
     * @param string $type
     * @return JsonResponse
     */
    public function gestionInducement(
        EquipeService $equipeService,
        PlayerService $playerService,
        string $action,
        int $teamId,
        string $type
    ): \Symfony\Component\HttpFoundation\JsonResponse {
        return TransformeEnJSON::transforme(
            $equipeService->gestionInducement($teamId, $action, $type, $playerService)
        );
    }

    /**
     * @Route("/checkteam/{teamId}", name="Checkteam")
     * @param int $teamId
     * @param EquipeService $equipeService
     * @param PlayerService $playerService
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function checkTeam(
        int $teamId,
        EquipeService $equipeService,
        PlayerService $playerService
    ): \Symfony\Component\HttpFoundation\RedirectResponse {
        /** @var Teams $team */
        $team = $this->getDoctrine()->getRepository(Teams::class)->findOneBy(['teamId' => $teamId]);

        if (!empty($team)) {
            $equipeService->checkEquipe($team, $playerService);

            return $this->redirectToRoute('team', ['teamid' => $team->getTeamId()]);
        }

        return $this->redirectToRoute('frontUser');
    }

    /**
     * @Route("/changeNomStade", name="changeNomStade", options = { "expose" = true })
     * @return Response
     */
    public function changeNomStade(
        Request $request,
        AdminService $adminService
    ): Response {
        $adminService->traiteModification($request->request->all(), Stades::class);

        return new Response();
    }

    /**
     * @Route("/ajoutStadeModal/{equipeId}", name="ajoutStadeModal")
     * @param Request $request
     * @param int $equipeId
     * @param StadeService $stadeService
     * @return Response
     */
    public function ajoutStadeModal(
        Request $request,
        int $equipeId,
        StadeService $stadeService
    ): Response {
        /** @var Teams $equipe */
        $equipe = $this->getDoctrine()->getRepository(Teams::class)->findOneBy(['teamId' => $equipeId]);
        $stade = $equipe->getFStades();

        $form = $this->createForm(CreerStadeType::class, $stade);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $stadeService->creerStade($request, $equipe);

            return $this->redirectToRoute('team', ['teamid' => $equipe->getTeamId()]);
        }

        return $this->render(
            'statbb/ajoutStade.html.twig',
            ['form' => $form->createView(), 'teamId' => $equipe->getTeamId()]
        );
    }

    /**
     * @Route("/recalculerTV", name="recalculerTV")
     * @param EquipeService $equipeService
     * @param PlayerService $playerService
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function recalculerTV(
        EquipeService $equipeService,
        PlayerService $playerService
    ): \Symfony\Component\HttpFoundation\RedirectResponse {
        $entityManager = $this->getDoctrine()->getManager();

        /** @var Teams $equipe */
        foreach ($this->getDoctrine()->getRepository(Teams::class)->findAll() as $equipe) {
            $equipe->setTv($equipeService->tvDelEquipe($equipe, $playerService));

            $entityManager->persist($equipe);
            $entityManager->flush();
        }

        return $this->redirectToRoute('frontUser');
    }

    /**
     * @Route("/listeDesJoueurs/{equipe}", name="listeDesJoueurs")
     * @param Teams $equipe
     * @return Response
     */
    public function listeDesJoueurs(Teams $equipe): \Symfony\Component\HttpFoundation\Response
    {
        return $this->render(
            'statbb/playerAdderTable.html.twig',
            [
                'listeJoueurs' => $this->getDoctrine()
                    ->getRepository(Players::class)
                    ->listeDesJoueursActifsPourlEquipe($equipe)
            ]
        );
    }

    /**
     * @route("/supprimeLogo/{equipeId}", name="supprimeLogo", options = { "expose" = true })
     * @param int $equipeId
     * @return Response
     */
    public function supprimeLogo(int $equipeId, EquipeService $equipeService): \Symfony\Component\HttpFoundation\Response
    {
        $equipeService->supprimerLogo(
            $this->getDoctrine()
                ->getRepository(Teams::class)
                ->findOneBy(['teamId' => $equipeId]),
            $this->getParameter('logo_directory')
        );

        return new Response();
    }

    /**
     * @route("/mettreEnFranchise/{equipeId}", name="mettreEnFranchise")
     * @param int $equipeId
     * @return Response
     */
    public function mettreEnFranchise(int $equipeId, EquipeService $equipeService): Response
    {
        /** @var Teams $equipe */
        $equipe = $this->getDoctrine()->getRepository(Teams::class)->findOneBy(['teamId' => $equipeId]);

        $equipeService->mettreEnFranchise($equipe);

        return $this->redirectToRoute('team', ['teamid' => $equipeId]);
    }
}
