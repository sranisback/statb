<?php

namespace App\Controller;

use App\Entity\Coaches;
use App\Entity\GameDataStadium;
use App\Entity\Matches;
use App\Entity\Players;
use App\Entity\Teams;
use App\Enum\AnneeEnum;
use App\Form\CreerStadeType;
use App\Form\LogoEnvoiType;
use App\Service\EquipeService;
use App\Service\PlayerService;
use App\Service\SettingsService;
use App\Form\CreerEquipeType;

use App\Service\StadeService;
use Gumlet\ImageResize;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class EquipeController extends AbstractController
{
    /**
     * @param mixed $response
     * @return JsonResponse
     */
    public static function transformeEnJson($response): JsonResponse
    {
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        $serializer = new Serializer($normalizers, $encoders);

        $jsonContent = $serializer->serialize($response, 'json');

        return new JsonResponse($jsonContent);
    }

    /**
     * @Route("/montreLesEquipes", name="showteams", options = { "expose" = true })
     * @param SettingsService $settingsService
     * @return response
     */
    public function montreLesEquipes(SettingsService $settingsService)
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
     * @Route("/montreLesAnciennesEquipes/{coachActif}", name="showOldTeams", options = { "expose" = true})
     * @param SettingsService $settingsService
     * @param EquipeService $equipeService
     * @param integer $coachActif
     * @return response
     */
    public function montreLesAnciennesEquipes(
        SettingsService $settingsService,
        EquipeService $equipeService,
        $coachActif
    ) {
        $annee = $settingsService->anneeCourante();

        $etiquetteAnne = (new AnneeEnum)->numeroToAnnee();

        $compilEquipes = [];

        /** @var Teams $equipe */
        foreach ($equipeService->listeDesAnciennesEquipes($coachActif, $annee) as $equipe) {
            $compilEquipes[] = [
                'equipe' => $equipe,
                'resultats' => $equipeService->resultatsDelEquipe(
                    $equipe,
                    $this->getDoctrine()->getRepository(Matches::class)->listeDesMatchs($equipe)
                ),
                'annee' => $etiquetteAnne[$equipe->getYear()],
            ];
        }

        return $this->render(
            'statbb/tabs/coach/anciennesEquipes.html.twig',
            ['listeEquipe' => $compilEquipes]
        );
    }

    /**
     * @Route("/showuserteams", name="showuserteams", options = { "expose" = true })
     * @param SettingsService $settingsService
     * @param EquipeService $equipeService
     * @return response
     */
    public function showUserTeams(
        SettingsService $settingsService,
        EquipeService $equipeService
    ) {
        $annee = $settingsService->anneeCourante();
        /** @var Coaches $coach */
        $coach = $this->getUser();
        $equipesEtResultatsDuCoach = [];

        foreach ($this->getDoctrine()->getRepository(Teams::class)->toutesLesEquipesDunCoachParAnnee(
            $coach->getCoachId(),
            $annee
        ) as $equipe) {
            $equipesEtResultatsDuCoach[] = [
                'equipe' => $equipe,
                'resultats' => $equipeService->resultatsDelEquipe(
                    $equipe,
                    $this->getDoctrine()->getRepository(Matches::class)->listeDesMatchs($equipe)
                ),
            ];
        }

        return $this->render(
            'statbb/tabs/coach/user_teams.html.twig',
            ['listeEquipe' => $equipesEtResultatsDuCoach]
        );
    }

    /**
     * @Route("/team/{teamid}", name="team", requirements={"teamid"="\d+"}, options = { "expose" = true })
     * @param PlayerService $playerService
     * @param EquipeService $equipeService
     * @param int $teamid
     * @return Response
     */
    public function showTeam(PlayerService $playerService, EquipeService $equipeService, $teamid, SettingsService $settingsService)
    {
        $pdata = [];

        /** @var Teams $equipe */
        $equipe = $this->getDoctrine()->getRepository(Teams::class)->findOneBy(['teamId' => $teamid]);

        /** @var Players $players */
        $players = $this->getDoctrine()->getRepository(Players::class)->listeDesJoueursPourlEquipe($equipe);

        $count = 0;

        /** @var Players $joueur */
        foreach ($players as $joueur) {
            $ficheJoueur = $playerService->statsDuJoueur($joueur);

            $pdata[$count]['pid'] = $joueur->getPlayerId();
            $pdata[$count]['nbrm'] = $ficheJoueur['actions']['NbrMatch'];
            $pdata[$count]['cp'] = $ficheJoueur['actions']['cp'];
            $pdata[$count]['td'] = $ficheJoueur['actions']['td'];
            $pdata[$count]['int'] = $ficheJoueur['actions']['int'];
            $pdata[$count]['cas'] = $ficheJoueur['actions']['cas'];
            $pdata[$count]['mvp'] = $ficheJoueur['actions']['mvp'];
            $pdata[$count]['agg'] = $ficheJoueur['actions']['agg'];
            $pdata[$count]['skill'] = $ficheJoueur['comp'];
            $pdata[$count]['spp'] = $playerService->xpDuJoueur($joueur);
            $pdata[$count]['cost'] = $playerService->valeurDunJoueur($joueur);
            $pdata[$count]['status'] = $playerService->statutDuJoueur($joueur);

            if (!$joueur->getName()) {
                $joueur->setName('Inconnu');
            }

            $count++;
        }

        $inducement = $equipeService->valeurInducementDelEquipe($equipe);

        $tdata['playersCost'] = $playerService->coutTotalJoueurs($equipe);
        $tdata['rerolls'] = $inducement['rerolls'];
        $tdata['pop'] = $inducement['pop'];
        $tdata['asscoaches'] = $inducement['asscoaches'];
        $tdata['cheerleader'] = $inducement['cheerleader'];
        $tdata['apo'] = $inducement['apo'];
        $tdata['tv'] = $equipeService->tvDelEquipe($equipe, $playerService);

        $form = $this->createForm(LogoEnvoiType::class, $equipe);

        return $this->render(
            'statbb/team.html.twig',
            [
                'players' => $players,
                'team' => $equipe,
                'pdata' => $pdata,
                'tdata' => $tdata,
                'form' => $form->createView(),
                'annee' => $settingsService->anneeCourante()
            ]
        );
    }

    /**
     * @Route("/team/{nomEquipe}", name="montreEquipe",
     *     requirements={"nommEquipe" = "\D+"}, options = { "expose" = true })
     * @param string $nomEquipe
     * @return Response
     */
    public function montreEquipe(string $nomEquipe, SettingsService $settingsService)
    {
        /** @var Teams[] $equipe */
        $equipe = $this->getDoctrine()->getRepository(Teams::class)->requeteEquipeLike($nomEquipe);

        if (count($equipe) > 1) {
            return $this->render(
                'statbb/didYouMean.html.twig',
                ['listeEquipe' => $equipe, 'annees' => (new AnneeEnum)->numeroToAnnee()]
            );
        }
        if ($equipe) {
            return $this->redirectToRoute('team', ['teamid' => $equipe[0]->getTeamId()]);
        }
        return $this->render('statbb/front.html.twig', ['annee' => $settingsService->anneeCourante()]);
    }

    /**
     * @Route("/uploadLogo/{equipeId}", name="uploadLogo")
     */
    public function uploadLogo(Request $request, $equipeId)
    {
        $form = $request->files->all();

        /** @var UploadedFile $logo */
        $logo = $form['logo_envoi']['logo'];

        $logo->move($this->getParameter('logo_directory'), $logo->getClientOriginalName());

        $image = new ImageResize($this->getParameter('logo_directory') . '/' . $logo->getClientOriginalName());
        $image->resizeToBestFit(200, 114);
        $image->save($this->getParameter('logo_directory') . '/' . $logo->getClientOriginalName());

        /** @var Teams $equipe */
        $equipe = $this->getDoctrine()->getRepository(Teams::class)->findOneBy(['teamId' => $equipeId]);

        $equipe->setLogo($logo->getClientOriginalName());

        $this->getDoctrine()->getManager()->persist($equipe);
        $this->getDoctrine()->getManager()->flush();

        $this->getDoctrine()->getManager()->refresh($equipe);

        return $this->redirectToRoute('team', ['teamid' => $equipe->getTeamId()]);
    }

    /**
     * @Route("/createTeam", name="createTeam", options = { "expose" = true })
     * @param Request $request
     * @param EquipeService $equipeService
     * @return Response
     */
    public function createTeam(Request $request, EquipeService $equipeService)
    {
        $coach = $this->getUser();

        $form = $request->request->get('creer_equipe');

        $teamid = 0;

        if ($coach) {
            /** @var Coaches $coach */
            $teamid = $equipeService->createTeam($form['Name'], $coach->getCoachId(), $form['fRace']);
        }

        if ($teamid != 0) {
            $this->addFlash('success', 'Equipe Ajoutée!');
        }

        return $this->redirectToRoute('team', ['teamid' => $teamid]);
    }

    /**
     * @Route("/choixRace", options = { "expose" = true })
     * @return Response
     */
    public function choixRace()
    {
        $equipe = new Teams();

        $form = $this->createForm(CreerEquipeType::class, $equipe);

        return $this->render('statbb/addteam.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/retTeam/{teamId}", options = { "expose" = true })
     * @param int $teamId
     * @return JsonResponse
     */
    public function retTeam($teamId)
    {
        $entityManager = $this->getDoctrine()->getManager();

        /** @var Teams $team */
        $team = $this->getDoctrine()->getRepository(Teams::class)->findOneBy(['teamId' => $teamId]);
        $team->setRetired(true);

        $entityManager->persist($team);
        $entityManager->flush();

        return self::transformeEnJson([]);
    }

    /**
     * @Route("/gestionInducement/{action}/{teamId}/{type}", options = { "expose" = true })
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
        $action,
        $teamId,
        $type
    ) {
        /** @var Teams $equipe */
        $equipe = $this->getDoctrine()->getRepository(Teams::class)->findOneBy(['teamId' => $teamId]);

        if ($action == 'add') {
            $coutEtnbr = $equipeService->ajoutInducement($equipe, $type, $playerService);
        } else {
            $coutEtnbr = $equipeService->supprInducement($equipe, $type, $playerService);
        }
        $tv = $equipeService->tvDelEquipe($equipe, $playerService);

        $response = [
            "tv" => $tv,
            "ptv" => $tv / 1000,
            "tresor" => $equipe->getTreasury(),
            "inducost" => $coutEtnbr['inducost'],
            "type" => $type,
            "nbr" => $coutEtnbr['nbr'],
        ];

        return self::transformeEnJson($response);
    }

    /**
     * @Route("/chkteam/{teamId}", name="Chkteam", options = { "expose" = true })
     * @param int $teamId
     * @param EquipeService $equipeService
     * @param PlayerService $playerService
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function chkteam($teamId, EquipeService $equipeService, PlayerService $playerService)
    {
        /** @var Teams $team */
        $team = $this->getDoctrine()->getRepository(Teams::class)->findOneBy(['teamId' => $teamId]);

        if (!empty($team)) {
            $equipeService->checkEquipe($team, $playerService);

            return $this->redirectToRoute('team', ['teamid' => $team->getTeamId(), 'type' => 'n'], 302);
        }

        return $this->redirectToRoute('/');
    }

    /**
     * @Route("/changeNomStade/{equipeId}/{nouveauNomStade}", name="changeNomStade", options = { "expose" = true })
     * @param StadeService $stadeService
     * @param int $equipeId
     * @param string $nouveauNomStade
     * @return Response
     */
    public function changeNomStade(StadeService $stadeService, $equipeId, $nouveauNomStade)
    {
        /** @var Teams $equipe */
        $equipe = $this->getDoctrine()->getRepository(Teams::class)->findOneBy(['teamId' => $equipeId]);
        $stadeService->renommerStade($equipe, $nouveauNomStade);

        $response = new Response();
        $response->setContent($equipeId);
        $response->setStatusCode(200);

        return $response;
    }

    /**
     * @Route("/ajoutStadeModal/{equipeId}", name="ajoutStadeModal", options = { "expose" = true })
     * @param int $equipeId
     * @return Response
     */
    public function ajoutStadeModal($equipeId)
    {
        /** @var Teams $equipe */
        $equipe = $this->getDoctrine()->getRepository(Teams::class)->findOneBy(['teamId' => $equipeId]);

        $stade = $equipe->getFStades();

        $form = $this->createForm(CreerStadeType::class, $stade);

        return $this->render(
            'statbb/ajoutStade.html.twig',
            ['form' => $form->createView(), 'teamId' => $equipe->getTeamId()]
        );

        return new Response('');
    }

    /**
     * @Route("/ajoutStade/{equipeId}", name="ajoutStade", options = { "expose" = true })
     * @param Request $request
     * @param StadeService $stadeService
     * @param int $equipeId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function ajoutStade(Request $request, StadeService $stadeService, $equipeId)
    {
        /** @var Teams $equipe */
        $equipe = $this->getDoctrine()->getRepository(Teams::class)->findOneBy(['teamId' => $equipeId]);

        $form = $request->request->get('creer_stade');

        /** @var GameDataStadium $typeStade */
        $typeStade = $this->getDoctrine()->getRepository(GameDataStadium::class)->findOneBy(
            ['id' => $form['fTypeStade']]
        );

        $stadeService->construireStade($equipe, $form['nom'], $typeStade);

        return $this->redirectToRoute('team', ['teamid' => $equipeId, 'type' => 'n']);
    }

    /**
     * @Route("/recalculerTV", name="recalculerTV", options = { "expose" = true })
     * @param EquipeService $equipeService
     * @param PlayerService $playerService
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function recalculerTV(EquipeService $equipeService, PlayerService $playerService)
    {
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
     * @Route("/listeDesJoueurs/{equipe}", name="listeDesJoueurs", options = { "expose" = true })
     * @param Teams $equipe
     * @return Response
     */
    public function listeDesJoueurs(Teams $equipe)
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
     * @route("/supprimeLogo/{equipeId}", name="supprimeLogo",  options = { "expose" = true })
     * @param int $equipeId
     * @return Response
     */
    public function supprimeLogo(int $equipeId)
    {
        /** @var Teams $equipe */
        $equipe = $this->getDoctrine()->getRepository(Teams::class)->findOneBy(['teamId' => $equipeId]);

        $fileSystem = new Filesystem();
        $fileSystem->remove($this->getParameter('logo_directory') . '/' . $equipe->getLogo());

        $equipe->setLogo(null);

        $this->getDoctrine()->getManager()->persist($equipe);
        $this->getDoctrine()->getManager()->flush();

        $this->getDoctrine()->getManager()->refresh($equipe);

        return new Response('ok');
    }

    /**
     * @route("/mettreEnFranchise/{equipeId}", name="mettreEnFranchise", options = { "expose" = true })
     */
    public function mettreEnFranchise($equipeId)
    {
        /** @var Teams $equipe */
        $equipe = $this->getDoctrine()->getRepository(Teams::class)->findOneBy(['teamId' => $equipeId]);

        $equipe->setFranchise(!$equipe->getFranchise());

        $this->getDoctrine()->getManager()->persist($equipe);
        $this->getDoctrine()->getManager()->flush();
        $this->getDoctrine()->getManager()->refresh($equipe);

        return new Response('ok');
    }
}
