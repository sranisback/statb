<?php


namespace App\Controller;

use App\Entity\GameDataStadium;
use App\Entity\Matches;
use App\Entity\Meteo;
use App\Entity\Teams;
use App\Enum\AnneeEnum;
use App\Enum\NiveauStadeEnum;
use App\Service\EquipeService;
use App\Service\MatchesService;
use App\Service\PlayerService;
use App\Service\SettingsService;
use App\Tools\TransformeEnJSON;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MatchController extends AbstractController
{

    private ManagerRegistry $doctrine;

    private EquipeService $equipeService;

    public function __construct(ManagerRegistry $doctrine, EquipeService $equipeService)
    {
        $this->doctrine = $doctrine;
        $this->equipeService = $equipeService;
    }

    /**
     * @Route("/dropdownPlayer/{teamId}/{nbr}", name="dropdownPlayer", options = { "expose" = true })
     * @param Teams $equipe
     * @param int $nbr
     * @return JsonResponse
     * @throws \Exception
     */
    public function dropdownPlayer(Teams $equipe, int $nbr): JsonResponse
    {
        $response = [
            'html' => $this->renderView(
                'statbb/dropdownplayers.html.twig',
                [
                    'players' => $this->equipeService->getListActivePlayers($equipe),
                    'teamId' => $equipe->getTeamId(),
                    'nbr' => $nbr,
                    'ruleset' => $equipe->getRuleset()
                ]
            ),
        ];

        return TransformeEnJSON::transforme($response);
    }

    /**
     * @Route("/addGame", name="addGame",options = { "expose" = true })
     * @param MatchesService $matchesService
     * @param Request $request
     * @return JsonResponse
     */
    public function addGame(
        MatchesService $matchesService,
        Request $request
    ): JsonResponse {
        $recuperationDonneeForm = [];

        if (($contenu = $request->getContent()) !== '') {
            $recuperationDonneeForm = json_decode($contenu, true);
        }

        $resultat = $matchesService->enregistrerMatch($recuperationDonneeForm);

        if ($resultat['enregistrement'] !== []) {
            $url = $this->generateUrl('match', ['matchId' => $resultat['enregistrement']]);
            $this->addFlash('admin', 'Match enregistré, <a href= "'.$url.'"> Voir </a>');
        }
        if ($resultat['defis'] !== null) {
            $this->addFlash('admin', 'Un defis a été réalisé');
        }

        return TransformeEnJSON::transforme('ok');
    }

    /**
     * @Route("/ajoutMatch", name="ajoutMatch")
     * @param SettingsService $settingsService
     * @return Response
     */
    public function ajoutMatch(SettingsService $settingsService): Response
    {
        $teamsNotLocked = $this->doctrine->getRepository(Teams::class)->findBy(
            ['year' => $settingsService->anneeCourante(), 'locked' => false],
            ['name' => 'ASC']
        );

        $teamsLockedNull = $this->doctrine->getRepository(Teams::class)->findBy(
            ['year' => $settingsService->anneeCourante(), 'locked' => null],
            ['name' => 'ASC']
        );

        return $this->render(
            'statbb/ajoutMatch.html.twig',
            [
                'teams' => array_merge($teamsNotLocked, $teamsLockedNull),
                'meteos' => $this->doctrine->getRepository(Meteo::class)->findAll(),
                'stades' => $this->doctrine->getRepository(GameDataStadium::class)->findAll(),
                'numero' => $this->doctrine->getRepository(Matches::class)->numeroDeMatch(),
            ]
        );
    }

    /**
     * @Route("/match/{matchId}", name="match")
     * @param PlayerService $playerService
     * @param integer $matchId
     * @return Response
     */
    public function visualiseurDeMatch(PlayerService $playerService, int $matchId)
    : Response
    {
        /** @var Matches $match */
        $match = $this->getDoctrine()->getRepository(Matches::class)->findOneBy(['matchId' => $matchId]);
        if (!empty($match)) {
            $team1 = $match->getTeam1();
            $team2 = $match->getTeam2();
            if (!empty($team1) && !empty($team2)) {
                return $this->render(
                    'statbb/matchviewer.html.twig',
                    [
                        'match' => $match,
                        'actionEquipe1' => $playerService->toutesLesActionsDeLequipeDansUnMatch($match, $team1),
                        'actionEquipe2' => $playerService->toutesLesActionsDeLequipeDansUnMatch($match, $team2),
                        'niveauStade' => NiveauStadeEnum::numeroVersNiveauDeStade()
                    ]
                );
            }
        }

        return new Response('erreur');
    }

    /**
     * @Route("/anciensMatchs", name="anciensMatchs" )
     * @return Response
     */
    public function matchsDunCoach(MatchesService $matchesService)
    : Response
    {
        return $this->render(
            'statbb/tabs/coach/anciensMatchs.html.twig',
            [
                'listeMatchesParAns' => $matchesService->tousLesMatchesDunCoachParAnnee($this->getUser()),
                'EtiquettesAnnees' => AnneeEnum::numeroToAnnee(),
            ]
        );
    }

    /**
     * @Route("/matchsAnnee", name="matchsAnnee" )
     * @param SettingsService $settingsService
     * @return Response
     */
    public function afficherLesMatchs(SettingsService $settingsService): Response
    {
        $annee = $settingsService->anneeCourante();

        $matches = $this->getDoctrine()->getRepository(Matches::class)->tousLesMatchDuneAnneClassementChrono(
            $annee,
            'ASC'
        );

        return $this->render('statbb/tabs/ligue/matches.html.twig', ['matches' => $matches]);
    }

    /**
     * @Route("/recalculLeScore", name="recalculLeScore")
     */
    public function recalculLeScore(MatchesService $matchesService): RedirectResponse
    {
        $matchesService->recalculLeScore();

        $this->addFlash('admin', 'Score Recalculé');
        return $this->redirectToRoute('frontUser');
    }
}
