<?php

namespace App\Controller;

use App\Entity\Players;
use App\Entity\Setting;
use App\Entity\Stades;
use App\Entity\Teams;
use App\Enum\AnneeEnum;
use App\Enum\NiveauStadeEnum;
use App\Enum\RulesetEnum;
use App\Form\CreerEquipeType;
use App\Form\CreerStadeType;
use App\Form\LogoEnvoiType;
use App\Service\AdminService;
use App\Service\EquipeGestionService;
use App\Service\EquipeService;
use App\Service\SettingsService;
use App\Service\StadeService;
use App\Tools\TransformeEnJSON;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EquipeController extends AbstractController
{
    private ManagerRegistry $doctrine;

    private SettingsService $settingsService;

    private EquipeService $equipeService;

    private EquipeGestionService $equipeGestionService;

    private AdminService $adminService;

    private StadeService $stadeService;

    public function __construct(
        ManagerRegistry $doctrine,
        SettingsService $settingsService,
        EquipeService  $equipeService,
        EquipeGestionService $equipeGestionService,
        AdminService $adminService,
        StadeService $stadeService
    ) {
        $this->doctrine = $doctrine;
        $this->settingsService = $settingsService;
        $this->equipeService = $equipeService;
        $this->equipeGestionService = $equipeGestionService;
        $this->adminService = $adminService;
        $this->stadeService = $stadeService;
    }

    /**
     * @Route("/montreLesEquipes", name="showteams")
     * @return response
     */
    public function montreLesEquipes(): Response
    {
        return $this->render(
            'statbb/tabs/ligue/showteams.html.twig',
            [
                'teams' => $this->doctrine->getRepository(Teams::class)->findBy(
                    ['year' => $this->settingsService->anneeCourante(), 'retired' => false]
                ),
            ]
        );
    }

    /**
     * @Route("/montreLesAnciennesEquipes", name="showOldTeams")
     * @return response
     */
    public function montreLesAnciennesEquipes(): Response
    {
        return $this->render(
            'statbb/tabs/coach/anciennesEquipes.html.twig',
            [
                'listeEquipe' => $this->equipeService->compileLesEquipes($this->getUser()),
                'etiquettes' => RulesetEnum::numeroVersEtiquette()
            ]
        );
    }

    /**
     * @Route("/showuserteams", name="showuserteams")
     * @return response
     */
    public function showUserTeams(): Response
    {
        return $this->render(
            'statbb/tabs/coach/user_teams.html.twig',
            [
                'listeEquipe' => $this->equipeService->compileEquipesAnneeEnCours($this->getUser()),
                'etiquettes' => RulesetEnum::numeroVersEtiquette()
            ]
        );
    }

    /**
     * @Route("/team/{teamId}", name="team", requirements={"teamId"="\d+"}, options = { "expose" = true })
     * @param Teams $equipe
     * @return Response
     * @throws \Exception
     */
    public function showTeam(Teams $equipe): Response
    {
        return $this->render(
            'statbb/team.html.twig',
            [
                'feuille' => $this->equipeService->feuilleDequipeComplete($equipe),
                'niveauStade' => NiveauStadeEnum::numeroVersNiveauDeStade()
            ]
        );
    }

    /**
     * @Route("/team/{nomEquipe}", name="montreEquipe", options = { "expose" = true })
     * @param string $nomEquipe
     * @return Response
     */
    public function montreEquipe(string $nomEquipe): Response {
        if ($nomEquipe !== '') {
            /** @var Teams[] $equipes */
            $equipes = $this->doctrine->getRepository(Teams::class)->requeteEquipeLike($nomEquipe);

            if (count($equipes) > 1) {
                return $this->render(
                    'statbb/didYouMean.html.twig',
                    [
                        'listeEquipe' => $equipes,
                        'annees' => AnneeEnum::numeroToAnnee()
                    ]
                );
            }
            if ($equipes !== []) {
                return $this->redirectToRoute('team', ['teamId' => $equipes[0]->getTeamId()]);
            }
        }
        return $this->render('statbb/front.html.twig', ['annee' => $this->settingsService->anneeCourante()]);
    }

    /**
     * @Route("/uploadLogo/{teamId}", name="uploadLogo")
     * @param Request $request
     * @param Teams $equipe
     * @return Response
     * @throws \Gumlet\ImageResizeException
     */
    public function uploadLogo(Request $request, Teams $equipe) : Response
    {
        $form = $this->createForm(LogoEnvoiType::class, $equipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->equipeService->enregistreLogo($request, $this->getParameter('logo_directory'), $equipe);

            return $this->redirectToRoute('team', ['teamId' => $equipe->getTeamId()]);
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
     * @return RedirectResponse|Response
     */
    public function createTeam(Request $request) : Response
    {
        $equipe = new Teams();

        $currentRuleset = $this->doctrine->getRepository(Setting::class)->findOneBy(['name' => 'currentRuleset']);

        $form = $this->createForm(CreerEquipeType::class, $equipe, ['ruleset'=>$currentRuleset->getValue()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var array $datas */
            $datas = $request->request->get('creer_equipe');

            $coach = $this->getUser();

            if (isset($coach)) {
                $teamid = $this->equipeGestionService->creationEquipe(
                    (int)$currentRuleset->getValue(),
                    $datas[RulesetEnum::getChampRaceFromIntByRuleset($currentRuleset->getValue())],
                    $coach->getCoachId(),
                    $datas['Name']
                );
            }

            if (!empty($teamid) && $teamid !== 0) {
                $this->addFlash('success', 'Equipe Ajoutée!');
            }

            return $this->redirectToRoute('team', ['teamId' => $teamid]);
        }

        return $this->render(
            'statbb/addteam.html.twig',
            ['form' => $form->createView(), 'ruleset' => $currentRuleset->getValue()]
        );
    }

    /**
     * @Route("/retireEquipe/{teamId}", name="retireEquipe")
     * @param Teams $equipe
     * @return RedirectResponse
     */
    public function retireEquipe(Teams $equipe): RedirectResponse
    {
        $equipe->setRetired(true);

        $this->doctrine->getManager()->persist($equipe);
        $this->doctrine->getManager()->flush();

        return $this->redirectToRoute('frontUser');
    }

    /**
     * @Route("/gestionInducement/{action}/{teamId}/{type}", name="gestionInducement", options = { "expose" = true })
     * @param string $action
     * @param Teams $equipe
     * @param string $type
     * @return JsonResponse
     */
    public function gestionInducement(string $action, Teams $equipe, string $type): JsonResponse {
        return TransformeEnJSON::transforme($this->equipeService->gestionInducement($equipe, $action, $type));
    }

    /**
     * @Route("/checkteam/{teamId}", name="Checkteam")
     * @param Teams $equipe
     * @return RedirectResponse
     */
    public function checkTeam(Teams $equipe): RedirectResponse
    {
        if (!empty($equipe)) {
            $this->equipeGestionService->checkEquipe($equipe);

            return $this->redirectToRoute('team', ['teamId' => $equipe->getTeamId()]);
        }

        return $this->redirectToRoute('frontUser');
    }

    /**
     * @Route("/changeNomStade", name="changeNomStade", options = { "expose" = true })
     * @return Response
     */
    public function changeNomStade(Request $request): Response
    {
        $this->adminService->traiteModification($request->request->all(), Stades::class);

        return new Response();
    }

    /**
     * @Route("/ajoutStadeModal/{teamId}", name="ajoutStadeModal")
     * @param Request $request
     * @param Teams $equipe
     * @return Response
     */
    public function ajoutStadeModal(Request $request, Teams $equipe): Response
    {
        $stade = $equipe->getFStades();

        $form = $this->createForm(CreerStadeType::class, $stade);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->stadeService->creerStade($request, $equipe);

            return $this->redirectToRoute('team', ['teamid' => $equipe->getTeamId()]);
        }

        return $this->render(
            'statbb/ajoutStade.html.twig',
            ['form' => $form->createView(), 'teamId' => $equipe->getTeamId()]
        );
    }

    /**
     * @Route("/recalculerTV", name="recalculerTV")
     * @return RedirectResponse
     */
    public function recalculerTV() : RedirectResponse
    {
        /** @var Teams $equipe */
        foreach ($this->doctrine->getRepository(Teams::class)->findAll() as $equipe) {
            $equipe->setTv($this->equipeGestionService->tvDelEquipe($equipe));

            $this->doctrine->getManager()->persist($equipe);
            $this->doctrine->getManager()->flush();
        }

        return $this->redirectToRoute('frontUser');
    }


    /**
     * @Route("/recalculerTVAnneeActive", name="recalculerTVAnneeActive")
     * @return RedirectResponse
     */
    public function recalculerTVPourAnneActive(): RedirectResponse
    {
        /** @var Teams $equipe */
        foreach ($this->doctrine->getRepository(Teams::class)->findBy(['year' => $this->settingsService->anneeCourante()]) as $equipe) {
            $equipe->setTv($this->equipeGestionService->tvDelEquipe($equipe));

            $this->doctrine->getManager()->persist($equipe);
            $this->doctrine->getManager()->flush();
        }

        $this->addFlash('admin', 'Tv Recalculée pour année ' . AnneeEnum::numeroToAnnee()[$this->settingsService->anneeCourante()]);
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
                'listeJoueurs' => $this->doctrine
                    ->getRepository(Players::class)
                    ->listeDesJoueursActifsPourlEquipe($equipe)
            ]
        );
    }

    /**
     * @route("/supprimeLogo/{teamId}", name="supprimeLogo", options = { "expose" = true })
     * @param Teams $equipe
     * @return Response
     */
    public function supprimeLogo(Teams $equipe): Response
    {
        $this->equipeService->supprimerLogo($equipe, $this->getParameter('logo_directory'));
        return new Response();
    }

    /**
     * @route("/mettreEnFranchise/{teamId}", name="mettreEnFranchise")
     * @param Teams $equipe
     * @return Response
     */
    public function mettreEnFranchise(Teams $equipe): Response
    {
        $this->equipeGestionService->mettreEnFranchise($equipe);

        return $this->redirectToRoute('team', ['teamId' => $equipe->getTeamId()]);
    }
}
