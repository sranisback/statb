<?php

namespace App\Controller;

use App\Entity\Players;
use App\Entity\PlayersSkills;
use App\Entity\Setting;
use App\Entity\Teams;
use App\Enum\RulesetEnum;
use App\Form\AjoutCompetenceBb2020Type;
use App\Form\AjoutCompetenceType;
use App\Form\AjoutJoueurType;
use App\Form\JoueurPhotoEnvoiType;
use App\Service\AdminService;
use App\Service\EquipeService;
use App\Service\PlayerService;
use App\Tools\randomNameGenerator;
use App\Tools\TransformeEnJSON;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class JoueurController extends AbstractController
{
    /**
     * @Route("/player/{playerid}", name="Player")
     * @param int $playerid
     * @param PlayerService $playerService
     * @return Response
     */
    public function showPlayer(int $playerid, PlayerService $playerService): Response
    {
        $joueur = $this->getDoctrine()->getRepository(Players::class)->findOneBy(['playerId' => $playerid]);

        list($listeMatches, $msdata) = $playerService->listesDesActionsDunJoueurParMatches($joueur);

        return $this->render(
            'statbb/player.html.twig',
            [
                'player' => $joueur,
                'pdata' => $playerService->ligneJoueur([$joueur]),
                'matches' => $listeMatches,
                'mdata' => $msdata
            ]
        );
    }

    /**
     * @Route("/getposstat/{posId}", name="getposstat", options = { "expose" = true })
     * @param int $posId
     * @param PlayerService $playerService
     * @return Response
     */
    public function getposstat(int $posId, PlayerService $playerService, Request $request): Response
    {
        $donneesRuleset = $request->request->all();

        /** @var  class-string<object> $repo */
        $repo = RulesetEnum::getGameDataPlayersRepoFromIntRuleset($donneesRuleset['ruleset']);
        $champ = RulesetEnum::champIdGameDataPlayerFromIntRuleset($donneesRuleset['ruleset']);

        $position = $this->getDoctrine()
            ->getManager()
            ->getRepository($repo)
            ->findOneBy([$champ => $posId]);

        $html = $this->render(
            'statbb/affichePosition.html.twig',
            [
                'position' => $position,
                'competence' => $playerService->competencesDunePositon($position), /* @phpstan-ignore-line */
                'ruleset' => $donneesRuleset['ruleset']
            ]
        );

        $contenuHtml = $html->getContent();

        if(!$contenuHtml) {
            $contenuHtml = null;
        }

        return new response($contenuHtml);
    }

    /**
     * @Route("/playerAdder/{equipe}", name="playerAdder")
     * @param Teams $equipe
     * @return Response
     */
    public function playerAdder(Teams $equipe): Response
    {
        $form = $this->createForm(AjoutJoueurType::class, null, ['equipe' => $equipe]);

        return $this->render('statbb/playeradder.html.twig', ['form' => $form->createView(), 'equipe' => $equipe]);
    }

    /**
     * @Route("/addPlayer", name="addPlayer", options = { "expose" = true })
     * @param PlayerService $playerService
     * @param EquipeService $equipeService
     * @param Request $request
     * @return JsonResponse
     */
    public function addPlayer(
        PlayerService $playerService,
        EquipeService $equipeService,
        Request $request
    ): JsonResponse {
        /** @var array $donneesPourAjout */
        $donneesPourAjout = $request->request->all();
        $resultat = $playerService->ajoutJoueur(
            $donneesPourAjout['idPosition'],
            $donneesPourAjout['teamId'],
            $donneesPourAjout['nom'],
            $donneesPourAjout['nr'],
            $donneesPourAjout['ruleset']
        );
        $tresors = 0;
        $html = '';
        $coutjoueur = 0;
        $reponse = '';
        $tv = 0;

        if ($resultat['resultat'] == 'ok') {
            /** @var Players $joueur */
            $joueur = $resultat['joueur'];
            $position = RulesetEnum::getPositionFromPlayerByRuleset($joueur);
            if ($position !== null) {
                $competences = $playerService->listeDesCompdDeBasedUnJoueur($joueur);

                $competences = substr($competences, 0, strlen($competences) - 2);

                $cout = $position->getCost();

                if ($playerService->leJoueurEstDisposable($joueur) || $playerService->leJoueurEstFanFavorite($joueur)) {
                    $cout = 0;
                }

                $html = $this->render(
                    'statbb/lineteamsheet.html.twig',
                    ['position' => $position, 'player' => $joueur, 'skill' => $competences, 'cout' => $cout]
                )
                    ->getContent();

                $equipe = $joueur->getOwnedByTeam();

                $coutjoueur = $joueur->getValue();

                if ($playerService->leJoueurEstDisposable($joueur) || $playerService->leJoueurEstFanFavorite($joueur)) {
                    $coutjoueur = 0;
                }

                if ($equipe !== null) {
                    $tv = $equipeService->tvDelEquipe($equipe, $playerService);
                    $tresors = $equipe->getTreasury();
                }

                $reponse = 'ok';
            }
        } else {
            $html = $resultat['resultat'];
        }

        if (!empty($equipe)) {
            $equipe->setTv($tv);

            $this->getDoctrine()->getManager()->persist($equipe);

            $this->getDoctrine()->getManager()->flush();
        }

        $response = [
            "html" => $html,
            "tv" => $tv,
            "ptv" => ($tv / 1_000),
            "tresor" => $tresors,
            "playercost" => $coutjoueur,
            "reponse" => $reponse,
        ];

        return TransformeEnJSON::transforme($response);
    }

    /**
     * @Route("/remPlayer/{playerId}", name="remPlayer", options = { "expose" = true })
     * @param PlayerService $playerService
     * @param int $playerId
     * @return JsonResponse
     */
    public function remPlayer(
        PlayerService $playerService,
        int $playerId
    ): JsonResponse {
        $resultat[''] = '';
        /** @var Players $joueur */
        $joueur = $this->getDoctrine()->getRepository(Players::class)->findOneBy(['playerId' => $playerId]);

        $resultat = $playerService->renvoisOuSuppressionJoueur($joueur);

        $response = [
            "tv" => $resultat['tv'],
            "ptv" => ($resultat['tv'] / 1_000),
            "tresor" => $resultat['tresor'],
            "playercost" => $resultat['playercost'],
            "reponse" => $resultat['reponse']
        ];

        return TransformeEnJSON::transforme($response);
    }

    /**
     * @Route("/changeNomEtNumero", name="changeNomEtNumero", options = { "expose" = true })
     * @return Response
     */
    public function changeNomEtNumero(
        Request $request,
        AdminService $adminService
    ): Response {
        $adminService->traiteModification($request->request->all(), Players::class);

        return new Response();
    }

    /**
     * @Route("/skillmodal/{playerid}", name="skillmodal")
     * @param int $playerid
     * @return Response
     */
    public function skillmodal(int $playerid): Response
    {
        $competence = new PlayersSkills();

        $currentRuleset = $this->getDoctrine()->getRepository(Setting::class)->findOneBy(['name' => 'currentRuleset']);

        $form = $this->createForm(AjoutCompetenceType::class, $competence, ['ruleset' => $currentRuleset->getValue()]);

        return $this->render('statbb/skillmodal.html.twig', ['playerId' => $playerid, 'form' => $form->createView(), 'ruleset' => $currentRuleset->getValue()]);
    }

    /**
     * @Route("/ajoutComp/{playerid}", name="ajoutComp")
     * @param Request $request
     * @param PlayerService $playerService
     * @param int $playerid
     * @return RedirectResponse|string
     */
    public function ajoutComp(Request $request, PlayerService $playerService, int $playerid)
    {
        /** @var array $form */
        $form = $request->request->get('ajout_competence');

        /** @var Players $joueur */
        $joueur = $this->getDoctrine()->getRepository(\App\Entity\Players::class)->findOneBy(['playerId' => $playerid]);

        /** @var class-string<object> $repo */
        $repo = RulesetEnum::getGameDataSkillRepoFromPlayerByRuleset($joueur);

        $competence = $this->getDoctrine()->getRepository($repo)->findOneBy(
            [RulesetEnum::getGameDataPlayerChampIdFromPlayerByRuleset($joueur) => $form[RulesetEnum::getChampSkillFromIntByRuleset($joueur->getRuleset())]]
        );

        if (!empty($joueur)) {
            $equipe = $joueur->getOwnedByTeam();
        }

        if (!empty($competence)) {
            $retour = $joueur->getRuleset() == 0 ?
                $playerService->ajoutCompetence($joueur, $competence) :
                $playerService->ajoutCompetenceBb2020($joueur, $competence, array_key_exists('hasard', $form) ? $form['hasard'] : false); /* @phpstan-ignore-line */

            if($retour != 'ok') {
                $this->addFlash('fail', $retour);
            }
        }

        if (!empty($equipe)) {
            return $this->redirectToRoute('team', ['teamid' => $equipe->getTeamId()]);
        }

        return 'erreur';
    }

    /**
     * @Route("/genereNom", name="genereNom", options = { "expose" = true })
     * @return Response
     */
    public function genereNomJoueur(): Response
    {
        $generateurDeNom = new randomNameGenerator();
        $nom = $generateurDeNom->generateNames(1);

        return new Response($nom[0]);
    }

    /**
     * @Route("/genereNumero", name="genereNumero", options = { "expose" = true })
     * @param PlayerService $playerService
     * @param Request $request
     * @return Response
     */
    public function genereNumero(
        PlayerService $playerService,
        Request $request
    ): Response {
        $donnees = $request->request->all();
        /** @var Teams $equipe */
        $equipe = $this->getDoctrine()->getRepository(Teams::class)->findOneBy(['teamId' => $donnees['equipeId']]);
        if (!empty($equipe)) {
            return new Response((string)$playerService->numeroLibreDelEquipe($equipe));
        }

        return new Response((string)99);
    }

    /**
     * @Route("/uploadPhoto/{joueurId}", name="uploadPhoto")
     * @param Request $request
     * @param int $joueurId
     * @param PlayerService $playerService
     * @return RedirectResponse|Response
     */
    public function uploadPhoto(Request $request, int $joueurId, PlayerService $playerService)
    {
        $joueur = $this->getDoctrine()->getRepository(Players::class)->findOneBy(['playerId' => $joueurId]);

        $form = $this->createForm(
            JoueurPhotoEnvoiType::class,
            $joueur
        );

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $playerService->uploadPhotoJoueur($request, $joueur, $this->getParameter('photo_directory'));
            return $this->redirectToRoute('Player', ['playerid' => $joueur->getPlayerId()]);
        }

        return $this->render('statbb/addPhoto.html.twig', [
                'joueur' => $joueur,
                'form' => $form->createView()
            ]
        );
    }

    /**
     * @route("/supprimePhoto/{joueurId}", name="supprimePhoto", options = { "expose" = true })
     * @param int $joueurId
     * @return Response
     */
    public function supprimePhotos(int $joueurId): Response
    {
        /** @var Players $joueur */
        $joueur = $this->getDoctrine()->getRepository(Players::class)->findOneBy(['playerId' => $joueurId]);

        $fileSystem = new Filesystem();
        $fileSystem->remove($this->getParameter('photo_directory') . '/' . $joueur->getPhoto());

        $joueur->setPhoto(null);

        $this->getDoctrine()->getManager()->persist($joueur);
        $this->getDoctrine()->getManager()->flush();

        $this->getDoctrine()->getManager()->refresh($joueur);

        return new Response('ok');
    }
}
