<?php

namespace App\Controller;

use App\Entity\GameDataPlayers;
use App\Entity\GameDataSkills;
use App\Entity\Players;
use App\Entity\PlayersSkills;
use App\Entity\Teams;
use App\Form\AjoutCompetenceType;
use App\Form\AjoutJoueurType;
use App\Form\JoueurPhotoEnvoiType;
use App\Service\AdminService;
use App\Service\EquipeService;
use App\Service\PlayerService;
use App\Tools\randomNameGenerator;
use App\Tools\TransformeEnJSON;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;

class JoueurController extends AbstractController
{
    /**
     * @Route("/player/{playerid}", name="Player")
     * @param int $playerid
     * @param PlayerService $playerService
     * @return Response
     */
    public function showPlayer(int $playerid, PlayerService $playerService): \Symfony\Component\HttpFoundation\Response
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
    public function getposstat(int $posId, PlayerService $playerService): \Symfony\Component\HttpFoundation\Response
    {
        $position = $this->getDoctrine()
            ->getManager()
            ->getRepository(GameDataPlayers::class)
            ->findOneBy(['posId' => $posId]);

        $html = $this->render(
            'statbb/affichePosition.html.twig',
            [
                'position' => $position,
                'competence' => $playerService->competencesDunePositon($position)
            ]
        );

        return new response($html->getContent());
    }

    /**
     * @Route("/playerAdder/{equipe}", name="playerAdder")
     * @param Teams $equipe
     * @return Response
     */
    public function playerAdder(Teams $equipe): \Symfony\Component\HttpFoundation\Response
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
    ): \Symfony\Component\HttpFoundation\JsonResponse {
        $donneesPourAjout = $request->request->all();
        $resultat = $playerService->ajoutJoueur(
            $donneesPourAjout['idPosition'],
            $donneesPourAjout['teamId'],
            $donneesPourAjout['nom'],
            $donneesPourAjout['nr']
        );
        $tresors = 0;
        $html = '';
        $coutjoueur = 0;
        $reponse = '';
        $tv = 0;

        if ($resultat['resultat'] == 'ok') {
            /** @var Players $joueur */
            $joueur = $resultat['joueur'];
            $position = $joueur->getFPos();
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
    ): \Symfony\Component\HttpFoundation\JsonResponse {
        $resultat[''] = '';
        /** @var Players $joueur */
        $joueur = $this->getDoctrine()->getRepository(Players::class)->findOneBy(['playerId' => $playerId]);

        $resultat = $playerService->renvoisOuSuppressionJoueur($joueur);

        $response = array(
            "tv" => $resultat['tv'],
            "ptv" => ($resultat['tv'] / 1_000),
            "tresor" => $resultat['tresor'],
            "playercost" => $resultat['playercost'],
            "reponse" => $resultat['reponse'],
        );

        return TransformeEnJSON::transforme($response);
    }

    /**
     * @Route("/changeNomEtNumero", name="changeNomEtNumero", options = { "expose" = true })
     * @return Response
     */
    public function changeNomEtNumero(
        Request $request,
        AdminService $adminService
    ): \Symfony\Component\HttpFoundation\Response {
        $adminService->traiteModification($request->request->all(), Players::class);

        return new Response();
    }

    /**
     * @Route("/skillmodal/{playerid}", name="skillmodal")
     * @param int $playerid
     * @return Response
     */
    public function skillmodal(int $playerid): \Symfony\Component\HttpFoundation\Response
    {
        $competence = new PlayersSkills();

        $form = $this->createForm(AjoutCompetenceType::class, $competence);

        return $this->render('statbb/skillmodal.html.twig', ['playerId' => $playerid, 'form' => $form->createView()]);
    }

    /**
     * @Route("/ajoutComp/{playerid}", name="ajoutComp")
     * @param Request $request
     * @param PlayerService $playerService
     * @param int $playerid
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|string
     */
    public function ajoutComp(Request $request, PlayerService $playerService, int $playerid)
    {
        $form = $request->request->get('ajout_competence');

        /** @var Players $joueur */
        $joueur = $this->getDoctrine()->getRepository(\App\Entity\Players::class)->findOneBy(['playerId' => $playerid]);

        /** @var GameDataSkills $competence */
        $competence = $this->getDoctrine()->getRepository(GameDataSkills::class)->findOneBy(
            ['skillId' => $form['fSkill']]
        );

        if (!empty($competence) && !empty($joueur)) {
            $playerService->ajoutCompetence($joueur, $competence);
        }

        if (!empty($joueur)) {
            $equipe = $joueur->getOwnedByTeam();
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
    public function genereNomJoueur(): \Symfony\Component\HttpFoundation\Response
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
    ): \Symfony\Component\HttpFoundation\Response {
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
    public function supprimePhotos(int $joueurId): \Symfony\Component\HttpFoundation\Response
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
