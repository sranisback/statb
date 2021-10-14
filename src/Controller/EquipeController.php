<?php

namespace App\Controller;

use App\Entity\Coaches;
use App\Entity\Players;
use App\Entity\Setting;
use App\Entity\Stades;
use App\Entity\Teams;
use App\Enum\AnneeEnum;
use App\Enum\NiveauStadeEnum;
use App\Enum\RulesetEnum;
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
use Symfony\Component\HttpFoundation\RedirectResponse;
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
    public function montreLesEquipes(SettingsService $settingsService): Response
    {
        return $this->render(
            'statbb/tabs/ligue/showteams.html.twig',
            [
                'teams' => $this->getDoctrine()->getRepository(Teams::class)->findBy(
                    ['year' => $settingsService->anneeCourante(), 'retired' => false]
                ),
            ]
        );
    }

    /**
     * @Route("/montreLesAnciennesEquipes", name="showOldTeams")
     * @param EquipeService $equipeService
     * @return response
     */
    public function montreLesAnciennesEquipes(
        EquipeService $equipeService
    ): Response {
        return $this->render(
            'statbb/tabs/coach/anciennesEquipes.html.twig',
            [
                'listeEquipe' => $equipeService->compileLesEquipes($this->getUser()), /* @phpstan-ignore-line */
                'etiquettes' => RulesetEnum::numeroVersEtiquette()
            ]
        );
    }

    /**
     * @Route("/showuserteams", name="showuserteams")
     * @param EquipeService $equipeService
     * @return response
     */
    public function showUserTeams(
        EquipeService $equipeService
    ): Response {
        return $this->render(
            'statbb/tabs/coach/user_teams.html.twig',
            [
                'listeEquipe' => $equipeService->compileEquipesAnneeEnCours($this->getUser()),
                'etiquettes' => RulesetEnum::numeroVersEtiquette()
            ]
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
    ): Response {
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
    ): Response {
        if ($nomEquipe !== '') {
            /** @var Teams[] $equipes */
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
    public function uploadLogo(Request $request, EquipeService $equipeService, int $equipeId) : Response
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
     * @return RedirectResponse|Response
     */
    public function createTeam(
        Request $request,
        EquipeService $equipeService
    ) : Response {
        $equipe = new Teams();

        $currentRuleset = $this->getDoctrine()->getRepository(Setting::class)->findOneBy(['name' => 'currentRuleset']);

        $form = $this->createForm(CreerEquipeType::class, $equipe, ['ruleset'=>$currentRuleset->getValue()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var array $datas */
            $datas = $request->request->get('creer_equipe');

            $coach = $this->getUser();

            if (isset($coach)) {
                $teamid = $equipeService->createTeam(
                    $datas['Name'],
                    $coach->getCoachId(),
                    $datas[RulesetEnum::getChampRaceFromIntByRuleset($currentRuleset->getValue())],
                    (int)$currentRuleset->getValue()
                );
            }

            if (!empty($teamid) && $teamid !== 0) {
                $this->addFlash('success', 'Equipe Ajoutée!');
            }

            return $this->redirectToRoute('team', ['teamid' => $teamid]);
        }

        return $this->render(
            'statbb/addteam.html.twig',
            ['form' => $form->createView(), 'ruleset' => $currentRuleset->getValue()]
        );
    }

    /**
     * @Route("/retireEquipe/{teamId}", name="retireEquipe")
     * @param int $teamId
     */
    public function retireEquipe(int $teamId): RedirectResponse
    {
        $entityManager = $this->getDoctrine()->getManager();

        /** @var Teams $equipe */
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
     * @return RedirectResponse
     */
    public function checkTeam(
        int $teamId,
        EquipeService $equipeService,
        PlayerService $playerService
    ): RedirectResponse {
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
     * @return RedirectResponse
     */
    public function recalculerTV(
        EquipeService $equipeService,
        PlayerService $playerService
    ): RedirectResponse {
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
    public function listeDesJoueurs(Teams $equipe): Response
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
    public function supprimeLogo(int $equipeId, EquipeService $equipeService): Response
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
