<?php


namespace App\Controller;

use App\Entity\GameDataStadium;
use App\Entity\Matches;
use App\Entity\Meteo;
use App\Entity\Players;
use App\Entity\Teams;
use App\Service\MatchesService;
use App\Service\PlayerService;
use App\Service\SettingsService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class MatchController extends AbstractController
{
    /**
     * @param  mixed $response
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
     * @Route("/dropdownPlayer/{teamId}/{nbr}", options = { "expose" = true })
     * @param int $teamId
     * @param int $nbr
     * @return JsonResponse
     */
    public function dropdownPlayer($teamId, $nbr)
    {
        $equipe = $this->getDoctrine()->getRepository(Teams::class)->findOneBy(['teamId' => $teamId]);

        if ($equipe) {
            $response = [
                'html' => $this->renderView(
                    'statbb/dropdownplayers.html.twig',
                    [
                        'players' => $this->getDoctrine()->getRepository(
                            Players::class
                        )->listeDesJoueursActifsPourlEquipe($equipe),
                        'teamId' => $teamId,
                        'nbr' => $nbr,
                    ]
                ),
            ];

            return self::transformeEnJson($response);
        } else {
            return new JsonResponse(['error']);
        }
    }

    /**
     * @Route("/addGame", options = { "expose" = true })
     * @param MatchesService $matchesService
     * @param Request $request
     * @return JsonResponse
     */
    public function addGame(
        MatchesService $matchesService,
        Request $request
    ) {
        $recuperationDonneeForm = [];

        if ($contenu = $request->getContent()) {
            $recuperationDonneeForm = json_decode($contenu, true);
        }

        $resultat = $matchesService->enregistrerMatch($recuperationDonneeForm);

        if ($resultat['enregistrement']) {
            $url = $this->generateUrl('match', ['matchId' => $resultat['enregistrement']]);
            $this->addFlash('admin', 'Match enregistré, <a href= "'.$url.'"> Voir </a>');
        }
        if ($resultat['defis']) {
            $this->addFlash('admin', 'Un defis a été réalisé');
        }

        return self::transformeEnJson('ok');
    }

    /**
     * @Route("/ajoutMatch", name="ajoutMatch", options = { "expose" = true })
     * @param SettingsService $settingsService
     * @return Response
     */
    public function ajoutMatch(SettingsService $settingsService)
    {
        return $this->render(
            'statbb/ajoutMatch.html.twig',
            [
                'teams' => $this->getDoctrine()->getRepository(Teams::class)->findBy(
                    ['year' => $settingsService->anneeCourante()],
                    ['name' => 'ASC']
                ),
                'meteos' => $this->getDoctrine()->getRepository(Meteo::class)->findAll(),
                'stades' => $this->getDoctrine()->getRepository(GameDataStadium::class)->findAll(),
                'numero' => $this->getDoctrine()->getRepository(Matches::class)->numeroDeMatch()
            ]
        );
    }

    /**
     * @Route("/match/{matchId}", name="match", options ={"expose"= true})
     * @param PlayerService $playerService
     * @param integer $matchId
     * @return string|Response
     */
    public function visualiseurDeMatch(PlayerService $playerService, $matchId)
    {
        $match = $this->getDoctrine()->getRepository(Matches::class)->findOneBy(['matchId'=>$matchId]);
        if (!empty($match)) {
            $team1 = $match->getTeam1();
            $team2 = $match->getTeam2();
            if (!empty($team1) && !empty($team2)) {
                $actionEquipe1 = $playerService->toutesLesActionsDeLequipeDansUnMatch($match, $team1);
                $actionEquipe2 = $playerService->toutesLesActionsDeLequipeDansUnMatch($match, $team2);
                if (!empty($actionEquipe1) && !empty($actionEquipe1)) {
                    return $this->render(
                        'statbb/matchviewer.html.twig',
                        [
                            'match' => $match,
                            'actionEquipe1' => $actionEquipe1,
                            'actionEquipe2' => $actionEquipe2
                        ]
                    );
                }
            }
        }

        return 'erreur';
    }
}
