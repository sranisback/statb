<?php

namespace App\Controller;

use App\Entity\Coaches;
use App\Entity\GameDataStadium;
use App\Entity\Primes;
use App\Entity\Teams;
use App\Entity\Races;
use App\Entity\Players;
use App\Entity\PlayersSkills;
use App\Entity\GameDataSkills;
use App\Entity\GameDataPlayers;
use App\Entity\Matches;
use App\Entity\MatchData;
use App\Entity\Setting;

use App\Form\AjoutCompetenceType;
use App\Form\CreerEquipeType;
use App\Form\CreerStadeType;
use App\Form\PrimeType;
use App\Form\RealiserPrimeType;
use App\Service\CoachService;
use App\Service\EquipeService;
use App\Service\MatchDataService;
use App\Service\MatchesService;
use App\Service\PlayerService;
use App\Service\PrimeService;
use App\Service\SettingsService;
use App\Service\StadeService;

use DateTime;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * Class StatBBController
 * @package App\Controller
 */
class StatBBController extends AbstractController
{
    /**
     * @Route("/montreLesEquipes", name="showteams", options = { "expose" = true })
     * @param EquipeService $equipeService
     * @param SettingsService $settingsService
     * @return response
     */
    public function montreLesEquipes(EquipeService $equipeService, SettingsService $settingsService)
    {
        return $this->render(
            'statbb/showteams.html.twig',
            [
                'teams' => $this->getDoctrine()->getRepository(Teams::class)->findBy(
                    ['year' => $settingsService->anneeCourante()]
                ),
            ]
        );
    }

    /**
     * @Route("/showuserteams", name="showuserteams", options = { "expose" = true })
     * @param SettingsService $settingsService
     * @param CoachService $coachService
     * @param EquipeService $equipeService
     * @param PlayerService $playerService
     * @return response
     */
    public function showUserTeams(
        SettingsService $settingsService,
        CoachService $coachService,
        EquipeService $equipeService,
        PlayerService $playerService
    ) {

        $tdata = [];

        $equipesCollection = $coachService->listeDesEquipeDuCoach($this->getUser(), $settingsService->anneeCourante());

        $countEquipe = 0;

        foreach ($equipesCollection as $number => $equipe) {
            $resultats = $equipeService->resultatsDelEquipe($equipe, $equipeService->listeDesMatchs($equipe));
            $tdata[$countEquipe]['tId'] = $equipe->getTeamId();
            $tdata[$countEquipe]['win'] = $resultats['win'];
            $tdata[$countEquipe]['loss'] = $resultats['loss'];
            $tdata[$countEquipe]['draw'] = $resultats['draw'];
            $tdata[$countEquipe]['tv'] = $equipeService->tvDelEquipe($equipe, $playerService);

            $countEquipe++;
        }

        return $this->render('statbb/user_teams.html.twig', ['coachteam' => $equipesCollection, 'tdata' => $tdata]);
    }

    /**
     * @Route("/team/{teamid}/{type}", name="team", options = { "expose" = true })
     * @param PlayerService $playerService
     * @param EquipeService $equipeService
     * @param int $teamid
     * @param string $type
     * @return Response
     */
    public function showTeam(PlayerService $playerService, EquipeService $equipeService, $teamid, $type)
    {
        $pdata = [];

        $equipe = $this->getDoctrine()->getRepository(Teams::class)->findOneBy(['teamId' => $teamid]);

        if ($equipe) {
            $players = $playerService->listeDesJoueursDelEquipe($equipe);

            $count = 0;

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

            if ($type == "modal") {
                return $this->render(
                    'statbb/team_modal.html.twig',
                    ['players' => $players, 'team' => $equipe, 'pdata' => $pdata, 'tdata' => $tdata]
                );
            } else {
                return $this->render(
                    'statbb/team.html.twig',
                    [
                        'players' => $players,
                        'team' => $equipe,
                        'pdata' => $pdata,
                        'tdata' => $tdata,
                    ]
                );
            }
        }

        return $this->render('statbb/base.html.twig');
    }

    /**
     * @Route("/player/{playerid}/{type}", name="Player", options = { "expose" = true })
     * @param int $playerid
     * @param string $type
     * @param PlayerService $playerService
     * @return Response
     */
    public function showPlayer($playerid, $type, PlayerService $playerService)
    {
        $msdata = [];
        $pdata = [];
        $mdata = '';

        $joueur = $this->getDoctrine()->getRepository(Players::class)->findOneBy(['playerId' => $playerid]);

        if ($joueur) {
            $ficheJoueur = $playerService->statsDuJoueur($joueur);

            $pdata['nbrm'] = $ficheJoueur['actions']['NbrMatch'];
            $pdata['cp'] = $ficheJoueur['actions']['cp'];
            $pdata['td'] = $ficheJoueur['actions']['td'];
            $pdata['int'] = $ficheJoueur['actions']['int'];
            $pdata['cas'] = $ficheJoueur['actions']['cas'];
            $pdata['mvp'] = $ficheJoueur['actions']['mvp'];
            $pdata['agg'] = $ficheJoueur['actions']['agg'];
            $pdata['skill'] = substr($ficheJoueur['comp'], 0, strlen($ficheJoueur['comp']) - 2);
            $pdata['spp'] = $playerService->xpDuJoueur($joueur);
            $pdata['cost'] = $playerService->valeurDunJoueur($joueur);

            if (!$joueur->getName()) {
                $joueur->setName('Inconnu');
            }

            $mdata = $this->getDoctrine()->getRepository(MatchData::class)
                ->findBy(['fPlayer' => $joueur->getPlayerId()]);
        }

        $count = 0;
        $parties = [];

        if ($mdata) {
            foreach ($mdata as $matchData) {
                $actionsDuMatch = $playerService->actionDuJoueurDansUnMatch($matchData);

                $match = $matchData->getFMatch();

                if ($match) {
                    $parties[] = $match->getMatchId();

                    $msdata[$count]["mId"] = $match->getMatchId();
                    $msdata[$count]["data"] = substr($actionsDuMatch['rec'], 0, strlen($actionsDuMatch['rec']) - 2);
                }
                $count++;
            }
        }

        $matches = $this->getDoctrine()->getRepository(Matches::class)->findBy(['matchId' => $parties]);

        if ($matches) {
            if ($count == 0) {
                $msdata[$count]["mId"] = 0;
                $msdata[$count]["data"] = '';
            }
        }

        if ($type == "modal") {
            return $this->render(
                'statbb/player_modal.html.twig',
                [
                    'player' => $joueur,
                    'pdata' => $pdata,
                    'matches' => $matches,
                    'mdata' => $msdata,
                ]
            );
        } else {
            return $this->render(
                'statbb/player.html.twig',
                [
                    'player' => $joueur,
                    'pdata' => $pdata,
                    'matches' => $matches,
                    'mdata' => $msdata,
                ]
            );
        }
    }

    /**
     * @Route("/", name="index", options = { "expose" = true })
     * @return Response
     */
    public function index()
    {
        return $this->render('statbb/front.html.twig');
    }

    /**
     * @Route("/login", name="login", options = { "expose" = true })
     * @return Response
     */
    public function login()
    {
        return $this->render('statbb/front.html.twig');
    }

    /**
     * @Route("/logout", name="logout")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function logout()
    {
        return $this->redirectToRoute('index');
    }

    /**
     * @Route("/citation", options = { "expose" = true })
     * @param SettingsService $settingsService
     * @return Response
     */
    public function citation(SettingsService $settingsService)
    {
        return $this->render('statbb/citation.html.twig', ['citation' => $settingsService->tirerCitationAuHasard()]);
    }

    /**
     * @Route("/classement/general/", name="classementgen", options = { "expose" = true })
     * @param EquipeService $equipeService
     * @return Response
     */
    public function classGen(EquipeService $equipeService)
    {
        return $this->render('statbb/classement.html.twig', ['classement' => $equipeService->classementGeneral()]);
    }

    /**
     * @Route("/classement/{type}/{teamorplayer}/{limit}", name="classement", options = { "expose" = true })
     * @param string $type
     * @param string $teamorplayer
     * @param int $limit
     * @return Response
     */
    public function sClass($type, $teamorplayer, $limit)
    {
        $class = '';
        $title = '';

        $setting = $this->getDoctrine()->getRepository(Setting::class)->findOneBy(['name' => 'year']);

        if ($setting) {
            $matches_data = $this->getDoctrine()->getRepository(MatchData::class)->SClassement(
                $setting->getValue(),
                $type,
                $teamorplayer,
                $limit
            );

            if ($teamorplayer == 'player') {
                switch ($type) {
                    case 'bash':
                        $title = 'Le Bash Lord - Record CAS';
                        $class = 'class_bash';

                        break;

                    case 'td':
                        $title = 'Le Marqueur - Record TD';
                        $class = 'class_td';

                        break;

                    case 'xp':
                        $title = 'Le Meilleur - Record SPP';
                        $class = 'class_xp';

                        break;

                    case 'pass':
                        $title = 'La Main d\'or - Record Passes';
                        $class = 'class_pass';

                        break;

                    case 'foul':
                        $title = 'Le Tricheur - Record Fautes';
                        $class = 'class_foul';
                        break;
                }

                return $this->render(
                    'statbb/Spclassement.html.twig',
                    array(
                        'players' => $matches_data,
                        'title' => $title,
                        'class' => $class,
                        'type' => $type,
                        'teamorplayer' => $teamorplayer,
                        'limit' => $limit,
                    )
                );
            } else {
                switch ($type) {
                    case 'bash':
                        $title = 'Les plus méchants';
                        $class = 'class_Tbash';

                        break;

                    case 'td':
                        $title = 'Le plus de TD';
                        $class = 'class_Ttd';

                        break;


                    case 'dead':
                        $title = 'Fournisseurs de cadavres';
                        $class = 'class_Tdead';
                        break;

                    case 'foul':
                        $title = 'Les tricheurs';
                        $class = 'class_Tfoul';
                        break;
                }

                return $this->render(
                    'statbb/Stclassement.html.twig',
                    array(
                        'teams' => $matches_data,
                        'title' => $title,
                        'class' => $class,
                        'type' => $type,
                        'teamorplayer' => $teamorplayer,
                        'limit' => $limit,
                    )
                );
            }
        }

        return $this->render('stabb/base.html.twig');
    }

    /**
     * @Route("/totalcas", options = { "expose" = true })
     */
    public function totalCas()
    {
        $setting = $this->getDoctrine()->getRepository(Setting::class)->findOneBy(['name' => 'year']);

        $matches = $this->getDoctrine()->getRepository(Matches::class)->findAll();

        $total_cas = 0;

        if ($matches && $setting) {
            $total_cas = $this->getDoctrine()->getRepository(MatchData::class)->totalcas($setting->getValue());

            foreach ($matches as $number => $match) {
                $team1 = $match->getTeam1();
                $team2 = $match->getTeam2();
                if ($team1 && $team2) {
                    if ($team1->getYear() != $setting->getValue()
                        || $team2->getYear() != $setting->getValue()) {
                        unset($matches[$number]);
                    }
                }
            }
        }

        return new Response(
            '<strong>Total : '.$total_cas[0]['score'].' En '.count($matches).' Matches.</strong><br/>
			 <strong>Par Matches :  '.round($total_cas[0]['score'] / count($matches), 2).'</strong>'
        );
    }

    /**
     * @Route("/lastfive/{teamId}", options = { "expose" = true })
     * @param int|null $teamId
     * @return Response
     */
    public function lastfive($teamId = null)
    {
        $setting = $this->getDoctrine()->getRepository(Setting::class)->findOneBy(['name' => 'year']);

        $matches = $this->getDoctrine()->getRepository(Matches::class)->findBy(array(), array('dateCreated' => 'DESC'));

        if ($matches && $setting) {
            $games = null;

            foreach ($matches as $number => $match) {
                $team1 = $match->getTeam1();
                $team2 = $match->getTeam2();
                if ($team1 && $team2) {
                    if ($team1->getYear() != $setting->getValue()
                        || $team2->getYear() != $setting->getValue()) {
                        unset($matches[$number]);
                    }
                }
            }

            if ($teamId) {
                foreach ($matches as $number => $match) {
                    $team1 = $match->getTeam1();
                    $team2 = $match->getTeam2();
                    if ($team1 && $team2) {
                        if ($team2->getTeamId() == $teamId || $team1->getTeamId() == $teamId) {
                            $games[] = $match;
                        }
                    }
                }
            } else {
                for ($x = 0; $x < 5; $x++) {
                    $games[] = $matches[$x];
                }
            }

            return $this->render('statbb/lastfivesmatches.html.twig', array('games' => $games));
        }

        return $this->render('statbb/base.html.twig');
    }

    /**
     * @Route("/dyk", name="dyk", options = { "expose" = true })
     * @param SettingsService $settingsService
     * @return Response
     */
    public function dyk(SettingsService $settingsService)
    {
        return new Response($settingsService->tirerDYKauHasard());
    }

    /**
     * @Route("/getposstat/{posId}", name="getposstat", options = { "expose" = true })
     * @param PlayerService $playerService
     * @param int $posId
     * @return Response
     */
    public function getposstat(PlayerService $playerService, $posId)
    {
        $position = $this->getDoctrine()->getRepository(GameDataPlayers::class)->findOneBy(['posId' => $posId]);

        if ($position) {
            $competences = $playerService->listeDesCompdUnePosition($position);
            $competences = substr($competences, 0, strlen($competences) - 2);

            $render = '<table class="table" id="pos_table">
                                    <thead>
                                        <tr>
                                            <th>Quantité</th>
                                            <th>MA</th>
                                            <th>ST</th>
                                            <th>AG</th>
                                            <th>AV</th>
                                            <th>Comp</th>
                                            <th>Cost</th>
    
                                        </tr>
                                    </thead>
                                <tbody>
                                        <tr>
                                            <td>0 - '.$position->getQty().'</td>	
                                            <td>'.$position->getMa().'</td>										
                                            <td>'.$position->getSt().'</td>										
                                            <td>'.$position->getAg().'</td>										
                                            <td>'.$position->getAv().'</td>										
                                            <td>'.$competences.'</td>
                                            <td>'.$position->getCost().'</td>	
                                        </tr>
                                    </tbody>
                                </table>';

            return new Response($render);
        }
        $response = new Response();
        $response->setContent('');
        $response->setStatusCode(500);

        return $response;
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
            $teamid = $equipeService->createTeam($form['Name'], $coach->getCoachId(), $form['fRace']);
        }

        return $this->redirectToRoute('team', ['teamid' => $teamid, 'type' => 'n']);
    }

    /**
     * @Route("/playerAdder/{raceId}/{teamId}", name="playerAdder", options = { "expose" = true })
     * @param int $raceId
     * @param int $teamId
     * @return Response
     */
    public function playerAdder($raceId, $teamId)
    {
        $race = $this->getDoctrine()->getRepository(races::class)->findOneBy(array('raceId' => $raceId));

        $playerpositions = $this->getDoctrine()->getRepository(GameDataPlayers::class)->findBy(['fRace' => $race]);

        return $this->render(
            'statbb/playeradder.html.twig',
            ['playerpositions' => $playerpositions, 'teamId' => $teamId]
        );
    }

    /**
     * @Route("/addPlayer/{posId}/{teamId}", options = { "expose" = true })
     * @param PlayerService $playerService
     * @param int $posId
     * @param int $teamId
     * @return JsonResponse
     */
    public function addPlayer(PlayerService $playerService, EquipeService $equipeService, $posId, $teamId)
    {
        $resultat = $playerService->ajoutJoueur($posId, $teamId);
        $tresors = 0;
        $html = '';
        $coutjoueur = 0;
        $reponse = '';
        $tv = 0;

        if ($resultat['resultat'] == 'ok') {
            $joueur = $resultat['joueur'];
            $position = $joueur->getFPos();
            if ($position) {
                $competences = $playerService->listeDesCompdDeBasedUnJoueur($joueur);

                $html = $this->render(
                    'statbb/lineteamsheet.html.twig',
                    ['position' => $position, 'player' => $joueur, 'skill' => $competences]
                )
                    ->getContent();

                $equipe = $joueur->getOwnedByTeam();

                $coutjoueur = $joueur->getValue();

                if ($equipe) {
                    $tv = $equipeService->tvDelEquipe($equipe, $playerService);
                    $tresors = $equipe->getTreasury();
                }

                $reponse = 'ok';
            }
        } else {
            $html = $resultat['resultat'];
        }

        $response = [
            "html" => $html,
            "tv" => $tv,
            "ptv" => ($tv / 1000),
            "tresor" => $tresors,
            "playercost" => $coutjoueur,
            "reponse" => $reponse,
        ];

        return self::transformeEnJson($response);
    }

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
     * @Route("/remPlayer/{playerId}", options = { "expose" = true })
     * @param PlayerService $playerService
     * @param EquipeService $equipeService
     * @param int $playerId
     * @return JsonResponse
     */
    public function remPlayer(PlayerService $playerService, EquipeService $equipeService, $playerId)
    {
        $resultat[''] = '';
        $joueur = $this->getDoctrine()->getRepository(Players::class)->findOneBy(['playerId' => $playerId]);

        if ($joueur) {
            $resultat = $playerService->renvoisOuSuppressionJoueur($joueur, $equipeService);
        }
        $response = array(
            "tv" => $resultat['tv'],
            "ptv" => ($resultat['tv'] / 1000),
            "tresor" => $resultat['tresor'],
            "playercost" => $resultat['playercost'],
            "reponse" => $resultat['reponse'],
        );

        return self::transformeEnJson($response);
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
        $equipe = $this->getDoctrine()->getRepository(Teams::class)->findOneBy(['teamId' => $teamId]);
        if ($equipe) {
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

        return self::transformeEnJson(["rien" => '']);
    }

    /**
     * @Route("/retTeam/{teamId}", options = { "expose" = true })
     * @param int $teamId
     * @return JsonResponse
     */
    public function retTeam($teamId)
    {
        $team = $this->getDoctrine()->getRepository(Teams::class)->findOneBy(['teamId' => $teamId]);

        if ($team) {
            $entityManager = $this->getDoctrine()->getManager();

            $team->setRetired(true);

            $response = array();

            $entityManager->persist($team);
            $entityManager->flush();

            return self::transformeEnJson($response);
        }

        return self::transformeEnJson(["rien" => '']);
    }

    /**
     * @Route("/dropdownPlayer/{teamId}/{nbr}", options = { "expose" = true })
     * @param PlayerService $playerService
     * @param int $teamId
     * @param int $nbr
     * @return JsonResponse
     */

    public function dropdownPlayer(PlayerService $playerService, $teamId, $nbr)
    {
        $equipe = $this->getDoctrine()->getRepository(Teams::class)->findOneBy(['teamId' => $teamId]);

        if ($equipe) {
            $response = [
                'html' => $this->renderView(
                    'statbb/dropdownplayers.html.twig',
                    [
                        'players' => $playerService->listeDesJoueursActifsDelEquipe($equipe),
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
     * @param Request $request
     * @return JsonResponse
     */

    public function addGame(
        EquipeService $equipeService,
        SettingsService $settingsService,
        PlayerService $playerService,
        MatchesService $matchesService,
        MatchDataService $matchDataService,
        Request $request
    ) {
        $recuperationDonneeForm = [];

        if ($contenu = $request->getContent()) {
            $recuperationDonneeForm = json_decode($contenu, true);
        }

        $matchesService->enregistrerMatch($recuperationDonneeForm);

        return self::transformeEnJson('ok');
    }


    /**
     * @Route("/chkteam/{teamId}", name="Chkteam", options = { "expose" = true })
     * @param int $teamId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function chkteam($teamId)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $team = $this->getDoctrine()->getRepository(Teams::class)->findOneBy(['teamId' => $teamId]);

        if ($team) {
            $players = $this->getDoctrine()->getRepository(Players::class)
                ->findBy(['ownedByTeam' => $team->getTeamId()]);

            $positionJr = '';

            $teamRace = $team->getFRace();

            if ($teamRace) {
                if ($teamRace->getRaceId() == 17) {
                    $positionJr = $this->getDoctrine()->getRepository(GameDataPlayers::class)
                        ->findOneBy(['posId' => '171']);
                } else {
                    $positionJr = $this->getDoctrine()->getRepository(GameDataPlayers::class)->findOneBy(
                        ['fRace' => $team->getFRace(), 'qty' => '16']
                    );
                }
            }
            $number = 0;

            foreach ($players as $player) {
                if ($player->getStatus() != 7 && $player->getStatus() != 8 && $player->getInjRpm() != 1) {
                    $number++;
                }

                $mdata = $this->getDoctrine()->getRepository(MatchData::class)->findBy(
                    ['fPlayer' => $player->getPlayerId()]
                );

                $tcp = 0;
                $ttd = 0;
                $tint = 0;
                $tcas = 0;
                $tmvp = 0;
                $tagg = 0;

                foreach ($mdata as $game) {
                    $tcp += $game->getCp();
                    $ttd += $game->getTd();
                    $tint += $game->getIntcpt();
                    $tcas += ($game->getBh() + $game->getSi() + $game->getKi());
                    $tmvp += $game->getMvp();
                    $tagg += $game->getAgg();
                }

                $spp = $tcp + ($ttd * 3) + ($tint * 2) + ($tcas * 2) + ($tmvp * 5);

                $skills = $this->getDoctrine()->getRepository(PlayersSkills::class)->findBy(
                    ['fPid' => $player->getPlayerId()]
                );

                $nbrskill = 0;

                $nbrskill += count($skills) + $player->getAchAg() + $player->getAchAv()
                    + $player->getAchMa() + $player->getAchSt();

                switch ($nbrskill) {
                    case 0:
                        if ($spp > 5) {
                            $player->setStatus(9);
                        }
                        break;
                    case 1:
                        if ($spp > 15) {
                            $player->setStatus(9);
                        }
                        break;
                    case 2:
                        if ($spp > 30) {
                            $player->setStatus(9);
                        }
                        break;
                    case 3:
                        if ($spp > 50) {
                            $player->setStatus(9);
                        }
                        break;
                    case 4:
                        if ($spp > 75) {
                            $player->setStatus(9);
                        }
                        break;
                    case 5:
                        if ($spp > 175) {
                            $player->setStatus(9);
                        }
                        break;
                }
                $entityManager->persist($player);
            }

            if ($number < 11) {
                $diff = 11 - $number;

                $number = 16;

                for ($x = 1; $x < $diff + 1; $x++) {
                    $players = $this->getDoctrine()->getRepository(Players::class)->findBy(
                        ['ownedByTeam' => $team->getTeamId(), 'type' => 2],
                        ['nr' => 'ASC']
                    );

                    foreach ($players as $player) {
                        if ($number == $player->getNr()) {
                            $number++;
                        } else {
                            break;
                        }
                    }

                    $jr = new Players();

                    $jr->setNr($number);
                    $jr->setType(2);

                    $dateBoughtFormat = DateTime::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s"));

                    if ($dateBoughtFormat) {
                        $jr->setDateBought($dateBoughtFormat);
                    }

                    $jr->setOwnedByTeam($team);
                    $jr->setStatus(1);

                    $teamCoaches = $team->getOwnedByCoach();

                    if ($teamCoaches) {
                        $jr->setFCid($teamCoaches);
                    }

                    if ($positionJr) {
                        $positionRace = $positionJr->getFRace();
                        if ($positionRace) {
                            $jr->setFRid($positionRace);
                        }
                        $jr->setValue((int)$positionJr->getCost());
                        $jr->setFPos($positionJr);
                    }

                    $entityManager->persist($jr);
                    $entityManager->flush();

                    $number = 16;
                }
            } else {
                $players = $this->getDoctrine()->getRepository(Players::class)->findBy(
                    ['ownedByTeam' => $team->getTeamId(), 'type' => 2],
                    ['nr' => 'DESC']
                );

                foreach ($players as $player) {
                    $player->setStatus(7);

                    $dateSoldFormat = DateTime::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s"));

                    if ($dateSoldFormat) {
                        $player->setDateSold($dateSoldFormat);
                    }

                    $entityManager->persist($player);
                    $entityManager->flush();

                    $number--;
                    if ($number < 11) {
                        break;
                    }
                }
            }

            $entityManager->persist($team);
            $entityManager->flush();

            return $this->redirectToRoute('team', ['teamid' => $team->getTeamId(), 'type' => 'n'], 302);
        }

        return $this->redirectToRoute('index');
    }

    /**
     * @Route("/changeNr/{newnr}/{playerid}", name="changeNr", options = { "expose" = true })
     * @param int $newnr
     * @param int $playerid
     * @return Response
     */
    public function changeNr($newnr, $playerid)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $player = $this->getDoctrine()->getRepository(players::class)->findOneBy(['playerId' => $playerid]);

        if ($player) {
            $player->setNr($newnr);

            $entityManager->persist($player);
            $entityManager->flush();

            $playerTeamId = 0;

            $playerTeam = $player->getOwnedByTeam();

            if ($playerTeam) {
                $playerTeamId = $playerTeam->getTeamId();
            }
            $response = new Response();
            $response->setContent($playerTeamId);
            $response->setStatusCode(200);

            return $response;
        }
        $response = new Response();
        $response->setContent('');
        $response->setStatusCode(500);

        return $response;
    }

    /**
     * @Route("/changeName/{newname}/{playerid}", name="changeName", options = { "expose" = true })
     * @param string $newname
     * @param int $playerid
     * @return Response
     */
    public function changeName($newname, $playerid)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $player = $this->getDoctrine()->getRepository(players::class)->findOneBy(['playerId' => $playerid]);

        if ($player) {
            $player->setName($newname);

            $entityManager->persist($player);
            $entityManager->flush();

            $playerTeamId = 0;

            $playerTeam = $player->getOwnedByTeam();

            if ($playerTeam) {
                $playerTeamId = $playerTeam->getTeamId();
            }
            $response = new Response();
            $response->setContent($playerTeamId);
            $response->setStatusCode(200);

            return $response;
        }

        $response = new Response();
        $response->setContent('');
        $response->setStatusCode(500);

        return $response;
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
        $equipe = $this->getDoctrine()->getRepository(Teams::class)->findOneBy(['teamId' => $equipeId]);

        if ($equipe) {
            $stadeService->renommerStade($equipe, $nouveauNomStade);

            $response = new Response();
            $response->setContent($equipeId);
            $response->setStatusCode(200);

            return $response;
        }

        $response = new Response();
        $response->setContent('');
        $response->setStatusCode(500);

        return $response;
    }

    /**
     * @Route("/pdfTeam/{id}", name="pdfTeam", options = { "expose" = true })
     * @param PlayerService $playerService
     * @param EquipeService $equipeService
     * @param int $id
     */
    public function pdfTeam(PlayerService $playerService, EquipeService $equipeService, $id)
    {
        $equipe = $this->getDoctrine()->getRepository(Teams::class)->find($id);

        $count = 0;
        $html = '';

        $pdata = [];
        $pdata[] = [];

        if ($equipe) {
            $joueurCollection = $playerService->listeDesJoueursActifsDelEquipe($equipe);

            foreach ($joueurCollection as $joueur) {
                $listeCompetence = $playerService->toutesLesCompsdUnJoueur($joueur);
                $actionJoueur = $playerService->actionsDuJoueur($joueur);

                if (!$joueur->getName()) {
                    $joueur->setName('Inconnu');
                }

                $pdata[$count]['pid'] = $joueur->getPlayerId();
                $pdata[$count]['nbrm'] = $actionJoueur['NbrMatch'];
                $pdata[$count]['cp'] = $actionJoueur['cp'];
                $pdata[$count]['td'] = $actionJoueur['td'];
                $pdata[$count]['int'] = $actionJoueur['int'];
                $pdata[$count]['cas'] = $actionJoueur['cas'];
                $pdata[$count]['mvp'] = $actionJoueur['mvp'];
                $pdata[$count]['agg'] = $actionJoueur['agg'];
                $pdata[$count]['skill'] = substr($listeCompetence, 0, strlen($listeCompetence) - 2);
                $pdata[$count]['spp'] = $playerService->xpDuJoueur($joueur);
                if ($joueur->getInjRpm() != 0) {
                    $pdata[$count]['cost'] = '<s>'.$playerService->valeurDunJoueur($joueur).'</s>';
                } else {
                    $pdata[$count]['cost'] = $playerService->valeurDunJoueur($joueur);
                }
                $pdata[$count]['status'] = $playerService->statutDuJoueur($joueur);

                $count++;
            }

            $race = $equipe->getFRace();

            $costRr = $race ? $race->getCostRr() : 0;

            $tdata['playersCost'] = $playerService->coutTotalJoueurs($equipe);
            $tdata['rerolls'] = $equipe->getRerolls() * $costRr;
            $tdata['pop'] = ($equipe->getFf() + $equipe->getFfBought()) * 10000;
            $tdata['asscoaches'] = $equipe->getAssCoaches() * 10000;
            $tdata['cheerleader'] = $equipe->getCheerleaders() * 10000;
            $tdata['apo'] = $equipe->getApothecary() * 50000;
            $tdata['tv'] = $equipeService->tvDelEquipe($equipe, $playerService);

            $html = $this->renderView(
                'statbb/pdfteam.html.twig',
                [
                    'players' => $joueurCollection,
                    'team' => $equipe,
                    'pdata' => $pdata,
                    'tdata' => $tdata,
                ]
            );
        }

        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->setIsRemoteEnabled(true);
        $pdfOptions->setIsHtml5ParserEnabled(true);

        $dompdf = new Dompdf($pdfOptions);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'landscape');

        // Render the HTML as PDF
        $dompdf->render();

        if ($equipe) {
            $dompdf->stream($equipe->getName().'.pdf', ["Attachment" => true]);
        } else {
            $dompdf->stream('error.pdf', ["Attachment" => true]);
        }
    }

    /**
     * @Route("/frontUser", name="frontUser", options = { "expose" = true })
     */
    public function frontUser()
    {
        return $this->render('statbb/user.html.twig');
    }

    /**
     * @Route("/ajoutMatch", name="ajoutMatch", options = { "expose" = true })
     */
    public function ajoutMatch(SettingsService $settingsService)
    {
        return $this->render(
            'statbb/ajoutMatch.html.twig',
            [
                'teams' => $this->getDoctrine()->getRepository(Teams::class)->findBy(
                    ['year' => $settingsService->anneeCourante()]
                ),
            ]
        );
    }

    /**
     * @Route("/ajoutStadeModal/{equipeId}", name="ajoutStadeModal", options = { "expose" = true })
     * @param int $equipeId
     * @return Response
     */
    public function ajoutStadeModal($equipeId)
    {
        $equipe = $this->getDoctrine()->getRepository(Teams::class)->findOneBy(['teamId' => $equipeId]);

        if ($equipe) {
            /** @var Teams $equipe */
            $stade = $equipe->getFStades();

            $form = $this->createForm(CreerStadeType::class, $stade);

            return $this->render(
                'statbb/ajoutStade.html.twig',
                ['form' => $form->createView(), 'teamId' => $equipe->getTeamId()]
            );
        }

        $response = new Response();
        $response->setContent('');
        $response->setStatusCode(500);

        return $response;
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
        $equipe = $this->getDoctrine()->getRepository(Teams::class)->findOneBy(['teamId' => $equipeId]);

        $form = $request->request->get('creer_stade');

        if ($equipe) {
            $typeStade = $this->getDoctrine()->getRepository(GameDataStadium::class)->findOneBy(
                ['id' => $form['fTypeStade']]
            );

            if ($typeStade) {
                $stadeService->construireStade($equipe, $form['nom'], $typeStade);
            }
        }

        return $this->redirectToRoute('team', ['teamid' => $equipeId, 'type' => 'n']);
    }

    /**
     * @Route("/skillmodal/{playerid}", name="skillmodal", options = { "expose" = true })
     * @param int $playerid
     * @return Response
     */
    public function skillmodal($playerid)
    {
        $competence = new PlayersSkills();

        $form = $this->createForm(AjoutCompetenceType::class, $competence);

        return $this->render('statbb/skillmodal.html.twig', ['playerId' => $playerid, 'form' => $form->createView()]);
    }

    /**
     * @Route("/ajoutComp/{playerid}", name="ajoutComp", options = { "expose" = true })
     * @param Request $request
     * @param PlayerService $playerService
     * @param int $playerid
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function ajoutComp(Request $request, PlayerService $playerService, $playerid)
    {
        $form = $request->request->get('ajout_competence');

        $joueur = $this->getDoctrine()->getRepository(players::class)->findOneBy(['playerId' => $playerid]);

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
            return $this->redirectToRoute('team', ['teamid' => $equipe->getTeamId(), 'type' => 'n']);
        }
    }

    /**
     * @Route("/montreLeCimetierre", name="montreLeCimetierre", options = { "expose" = true })
     * @param SettingsService $settingsService
     * @return Response
     */
    public function montreLeCimetiere(SettingsService $settingsService)
    {
        return $this->render(
            'statbb/cimetiere.html.twig',
            [
                'joueurCollection' => $this->getDoctrine()->getRepository(players::class)->mortPourlAnnee(
                    $settingsService->anneeCourante()
                ),
            ]
        );
    }

    /**
     * @Route("/montreClassementELO", name="montreClassementELO", options = { "expose" = true })
     * @param SettingsService $settingsService
     * @return Response
     */
    public function montreClassementELO(SettingsService $settingsService)
    {
        return $this->render(
            'statbb/classementELO.html.twig',
            [
                'equipeCollection' => $this->getDoctrine()->getRepository(Teams::class)->findBy(
                    ['year' => $settingsService->anneeCourante()]
                ),
            ]
        );
    }

    /**
     * @Route("/ajoutPrimeForm/{coachId}/{primeId}", name="ajoutPrimeForm", options = { "expose" = true })
     * @param int $coachId
     * @return Response
     */
    public function ajoutPrimeForm($coachId, $primeId = null)
    {
        $prime = new Primes();

        if ($primeId) {
            $prime = $this->getDoctrine()->getRepository(Primes::class)->findOneBy(['id' => $primeId]);
        }

        $form = $this->createForm(PrimeType::class, $prime, ['coach' => $coachId]);

        return $this->render('statbb/ajoutPrime.html.twig', ['form' => $form->createView(), 'coachId' => $coachId]);
    }

    /**
     * @Route("/ajoutPrime/{coachId}", name="ajoutPrime", options = { "expose" = true })
     * @param Request $request
     * @param PrimeService $primeService
     * @param int $coachId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function ajoutPrime(Request $request, PrimeService $primeService, $coachId)
    {
        $form = $request->request->get('prime');

        $primeService->creationPrime($coachId, $form);

        return $this->redirectToRoute('frontUser');
    }

    /**
     * @Route("/montrePrimesEnCours", name="montrePrimesEnCours", options = { "expose" = true })
     * @param SettingsService $settingsService
     * @return Response
     */
    public function montrePrimesEnCours(SettingsService $settingsService)
    {
        return $this->render(
            'statbb/affichagePrimes.html.twig',
            [
                'primeCollection' => $this->getDoctrine()->getRepository(Primes::class)->listePrimeEnCours(
                    $settingsService->anneeCourante()
                ),
            ]
        );
    }

    /**
     * @Route("/supprimerPrime/{primeId}", name="supprimerPrime", options = { "expose" = true })
     * @param PrimeService $primeService
     * @param int $primeId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function supprimerPrime(PrimeService $primeService, $primeId)
    {
        $primeService->supprimerPrime($primeId);

        return $this->redirectToRoute('frontUser');
    }

    /**
     * @Route("/realiserPrimeForm", name="realiserPrimeForm", options = { "expose" = true })
     */
    public function realiserPrimeForm(SettingsService $settingsService)
    {
        $prime = new Primes();

        $form = $this->createForm(RealiserPrimeType::class, $prime, ['year' => $settingsService->anneeCourante()]);

        return $this->render('statbb/realisationPrime.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/realiserPrime", name="realiserPrime", options = { "expose" = true })
     * @param Request $request
     */
    public function realiserPrime(Request $request, PrimeService $primeService)
    {
        $form = $request->request->get('realiser_prime');

        $primeService->realiserPrime($form);

        return $this->redirectToRoute('frontUser');
    }
}
