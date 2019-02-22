<?php /** @noinspection PhpUndefinedMethodInspection */

namespace App\Controller;

use App\Entity\Teams;
use App\Entity\Races;
use App\Entity\Players;
use App\Entity\PlayersSkills;
use App\Entity\GameDataSkills;
use App\Entity\GameDataPlayers;
use App\Entity\Citations;
use App\Entity\Matches;
use App\Entity\MatchData;
use App\Entity\Setting;
use App\Entity\Dyk;
use App\Entity\Coaches;

use App\Service\CoachService;
use App\Service\EquipeService;
use App\Service\PlayerService;
use App\Service\SettingsService;

use DateTime;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
 * Class StatBBController
 * @package App\Controller
 */
class StatBBController extends AbstractController
{
    /**
     * @Route("/showteams", name="showteams", options = { "expose" = true })
     * @param EquipeService $equipeService
     * @param SettingsService $settingsService
     * @return response
     */
    public function showTeams(EquipeService $equipeService, SettingsService $settingsService)
    {
        return $this->render(
            'statbb/showteams.html.twig',
            ['teams' => $equipeService->toutesLesTeamsParAnnee($settingsService->anneeCourante())]
        );
    }

    /**
     * @Route("/showuserteams", name="showuserteams", options = { "expose" = true })
     * @param SettingsService $settingsService
     * @param CoachService $coachService
     * @param EquipeService $equipeService
     * @return response
     */
    public function showUserTeams(
        SettingsService $settingsService,
        CoachService $coachService,
        EquipeService $equipeService
    ) {

        $tdata = [];

        $equipesCollection = $coachService->listeDesEquipeDuCoach($this->getUser(), $settingsService->anneeCourante());

        $countEquipe = 0;

        foreach ($equipesCollection as $equipe) {
            $tdata[$countEquipe]['tId'] = $equipe->getTeamId();

            $resultats = $equipeService->resultatsDelEquipe($equipe, $equipeService->listeDesMatchs($equipe));

            $tdata[$countEquipe]['win'] = $resultats['win'];
            $tdata[$countEquipe]['loss'] = $resultats['loss'];
            $tdata[$countEquipe]['draw'] = $resultats['draw'];

            $countEquipe++;
        }

        return $this->render('statbb/user_teams.html.twig', ['coachteam' => $equipesCollection, 'tdata' => $tdata]);
    }

    /**
     * @Route("/team/{teamid}/{type}", name="team", options = { "expose" = true })
     * @param int $teamid
     * @param string $type
     * @param PlayerService $playerService
     * @return Response
     */
    public function showTeam($teamid, $type, PlayerService $playerService)
    {
        $pdata = [];

        $equipe = $this->getDoctrine()->getRepository(Teams::class)->findOneBy(['teamId' => $teamid]);

        if ($equipe) {
            $players = $playerService->listeDesJoueursDelEquipe($equipe);

            $count = 0;

            $coutTotalJoueur = 0;

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
                $pdata[$count]['skill'] = substr($ficheJoueur['comp'], 0, strlen($ficheJoueur['comp']) - 2);
                $pdata[$count]['spp'] = $playerService->xpDuJoueur($joueur);
                $pdata[$count]['cost'] = $joueur->getValue();
                $pdata[$count]['status'] = $playerService->statutDuJoueur($joueur);

                if (!$joueur->getName()) {
                    $joueur->setName('Inconnu');
                }

                switch ($joueur->getStatus()) {
                    case 7:
                    case 8:
                        break;

                    default:
                        ($joueur->getInjRpm() != 0) ?/*rien faire*/ : $coutTotalJoueur += $joueur->getValue();
                        break;
                }


                $count++;
            }

            $equipeRace = $equipe->getFRace();

            if ($equipeRace) {
                $tdata['rerolls'] = $equipe->getRerolls() * $equipeRace->getCostRr();
            }

            $tdata['playersCost'] = $coutTotalJoueur;
            $tdata['pop'] = ($equipe->getFf() + $equipe->getFfBought()) * 10000;
            $tdata['asscoaches'] = $equipe->getAssCoaches() * 10000;
            $tdata['cheerleader'] = $equipe->getCheerleaders() * 10000;
            $tdata['apo'] = $equipe->getApothecary() * 50000;
            $tdata['tv'] = $coutTotalJoueur + $tdata['rerolls'] + $tdata['pop']
                + $tdata['asscoaches'] + $tdata['cheerleader'] + $tdata['apo'];

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

        $msdata= [];
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
            $pdata['cost'] = $joueur->getValue();

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
     * @Route("/admin")
     */
    public function admin()
    {
        return $this->render('statbb/admin.html.twig');
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
     */
    public function citation()
    {
        $citations = $this->getDoctrine()->getRepository(Citations::class)->findAll();

        $nbr = rand(1, count($citations) - 1);

        return $this->render('statbb/citation.html.twig', array('citation' => $citations[$nbr]));
    }

    /**
     * @Route("/touslescoaches", options = { "expose" = true })
     * @param Request $request
     * @return Response
     */
    public function tousLesCoaches(Request $request)
    {
        $coach = new coaches();
        $coach->setName('test');
        $coach->setPasswd('test');

        $form = $this->createFormBuilder($coach)->add('name', null)->add('passwd', null)->add(
            'save',
            SubmitType::class,
            ['label' => 'go']
        )->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', 'Genus created!');

            //    return $this->redirectToRoute('frontUser');
        } else {
            $this->addFlash('success', 'Genus created!');
        }

        return $this->render('statbb/listeCoaches.html.twig');
    }


    /**
     * @Route("/classement/general/{limit}", name="classementgen", options = { "expose" = true })
     * @param int $limit
     * @return Response
     */
    public function classGen($limit)
    {
        $setting = $this->getDoctrine()->getRepository(Setting::class)->findOneBy(['name' => 'year']);

        if ($setting) {
            $classement = $this->getDoctrine()->getRepository(Teams::class)->classement($setting->getValue(), $limit);

            return $this->render('statbb/classement.html.twig', ['classement' => $classement, 'limit' => $limit]);
        }
        return $this->render('stabb/base.html.twig');
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
     */
    public function dyk()
    {
        $dyk = $this->getDoctrine()->getRepository(Dyk::class)->findAll();

        $nbr = rand(1, count($dyk) - 1);

        return new Response(
            '<b>Did you know ?</b> <i>'.$dyk[$nbr]->getDykText().'</i>'
        );
    }

    /**
     * @Route("/getposstat/{posId}", name="getposstat", options = { "expose" = true })
     * @param int $posId
     * @return Response
     */
    public function getposstat($posId)
    {
        $position = $this->getDoctrine()->getRepository(GameDataPlayers::class)->findOneBy(['posId' => $posId]);
        if ($position) {
            $allskills = $this->getDoctrine()->getRepository(GameDataSkills::class)->findAll();

            $playerskills = explode(",", (string)$position->getSkills());

            $listskill = '';

            foreach ($playerskills as $playerskill) {
                foreach ($allskills as $baseskill) {
                    if ($baseskill->getSkillId() == $playerskill) {
                        $listskill .= $baseskill->getName().', ';
                    }
                }
            }

            if ($listskill == ', ') {
                $listskill = '';
            }

            $listskill = substr($listskill, 0, strlen($listskill) - 2);


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
                                            <td>'.$listskill.'</td>
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
     * @Route("/raceSelector", options = { "expose" = true })
     * @param Request $request
     * @return Response
     */
    public function raceSelector(Request $request)
    {
        $team = new Teams();

        $form = $this->createFormBuilder($team)
            ->add("Name", TextType::class, ['label'=>"Nom de l'équipe", 'required' => 'true'])
            ->add(
                'fRace',
                EntityType::class,
                ['class'=> Races::class,'choice_label' =>'name','label'=>'Choisir une Race']
            )
            ->add('submit', SubmitType::class, ['label' => 'Créer'])
            ->add('cancel', ButtonType::class, ['label'=>'Annuler','attr'=>['data-dismiss'=>'modal']])
            ->setAction($this->generateUrl('createTeam'))
            ->getForm();

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

        $form = $request->request->get('form');

        $teamid = 0;

        if ($coach) {
            $teamid =  $equipeService->createTeam($form['Name'], $coach->getCoachId(), $form['fRace']);
        }

        return $this->redirectToRoute('team', ['teamid'=>$teamid,'type'=>'n']);
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
     * @param EquipeService $equipeService
     * @param int $posId
     * @param int $teamId
     * @return JsonResponse
     */
    public function addPlayer(PlayerService $playerService, EquipeService $equipeService, $posId, $teamId)
    {
        $resultat = $equipeService->ajoutJoueur($playerService, $posId, $teamId);
        $tv = 0;
        $tresors = 0;
        $html = '';
        $coutjoueur = 0;
        $reponse = '';

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
                    $tv = $equipe->getTv();
                    $tresors = $equipe->getTreasury();
                }

                $reponse = 'ok';
            }
        } else {
            $html = $resultat['resultat'];
        }

        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);

        $response = array(
            "html" => $html,
            "tv" => $tv,
            "ptv" => ($tv / 1000),
            "tresor" => $tresors,
            "playercost" => $coutjoueur,
            "reponse" => $reponse
        );

        $jsonContent = $serializer->serialize($response, 'json');

        return new JsonResponse($jsonContent);
    }

    /**
     * @Route("/remPlayer/{playerId}", options = { "expose" = true })
     * @param int $playerId
     * @return JsonResponse
     */
    public function remPlayer($playerId)
    {
        $effect = "sld";

        $entityManager = $this->getDoctrine()->getManager();

        $player = $this->getDoctrine()->getRepository(Players::class)->findOneBy(['playerId' => $playerId]);

        if ($player) {
            $team = $player->getOwnedByTeam();

            $playerPosition = $player->getFPos();

            if ($team  && $playerPosition) {
                $matchest1 = $this->getDoctrine()->getRepository(Matches::class)->findBy(
                    ['team1' => $team->getTeamId()]
                );

                $matchest2 = $this->getDoctrine()->getRepository(Matches::class)->findBy(
                    ['team2' => $team->getTeamId()]
                );

                if (count($matchest1) == 0 || count($matchest2) == 0) {
                    $team->setTreasury(
                        $team->getTreasury() + $playerPosition->getCost()
                    );

                    $effect = "rm";
                }

                $tcost = 0;

                $supcomp = $this->getDoctrine()->getRepository(PlayersSkills::class)->findBy(
                    ['fPid' => $player->getPlayerId()]
                );

                if ($supcomp) {
                    foreach ($supcomp as $comps) {
                        if ($comps->getType() == 'N') {
                            $tcost += 20000;
                        } else {
                            $tcost += 30000;
                        }
                    }
                }

                if ($player->getAchMa() > 0) {
                    $tcost += 30000;
                }

                if ($player->getAchSt() > 0) {
                    $tcost += 50000;
                }

                if ($player->getAchAg() > 0) {
                    $tcost += 40000;
                }

                if ($player->getAchAv() > 0) {
                    $tcost += 30000;
                }

                $tv = $team->getTv() - $tcost;

                $team->setTv($tv);

                $encoders = array(new XmlEncoder(), new JsonEncoder());
                $normalizers = array(new ObjectNormalizer());

                $serializer = new Serializer($normalizers, $encoders);

                $treasury = $team->getTreasury();

                $response = array(
                    "tv" => $tv,
                    "ptv" => ($tv / 1000),
                    "tresor" => $treasury,
                    "playercost" => $tcost,
                    "reponse" => $effect,
                );

                if ($effect == "rm") {
                    $entityManager->remove($player);
                } else {
                    $player->setStatus(7);

                    $entityManager->persist($player);
                }

                $entityManager->flush();


                $jsonContent = $serializer->serialize($response, 'json');

                return new JsonResponse($jsonContent);
            }
        }
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize(["rien"=>''], 'json');

        return new JsonResponse($jsonContent);
    }

    /**
     * @Route("/addInducement/{teamId}/{type}", options = { "expose" = true })
     * @param int $teamId
     * @param string $type
     * @return JsonResponse
     */
    public function addInducement($teamId, $type)
    {
        $nbr = 0;
        $inducost = 0;
        $tv = 0;

        $team = $this->getDoctrine()->getRepository(Teams::class)->findOneBy(['teamId' => $teamId]);

        if ($team) {
            $entityManager = $this->getDoctrine()->getManager();

            $race = $team->getFRace();

            if ($race) {
                $costRr = $race->getCostRr();
            } else {
                $costRr = 0;
            }

            switch ($type) {
                case "rr":
                    $inducost = $costRr;

                    $matchest1 = $this->getDoctrine()->getRepository(Matches::class)->findBy(
                        ['team1' => $team->getTeamId()]
                    );

                    $matchest2 = $this->getDoctrine()->getRepository(Matches::class)->findBy(
                        ['team2' => $team->getTeamId()]
                    );

                    if (count($matchest1) > 0 || count($matchest2) > 0) {
                        $inducost = $inducost * 2;
                    }

                    if ($team->getTreasury() >= $inducost) {
                        $nbr = $team->getRerolls() + 1;

                        $team->setRerolls($nbr);

                        $treasury = $team->getTreasury() - $inducost;

                        $team->setTreasury($treasury);

                        $tv = $team->getTv() + $costRr;

                        $team->setTv($tv);

                        $inducost = $costRr;
                    }

                    break;

                case "pop":
                    if ($team->getTreasury() >= 10000) {
                        $matchest1 = $this->getDoctrine()->getRepository(Matches::class)->findBy(
                            ['team1' => $team->getTeamId()]
                        );

                        $matchest2 = $this->getDoctrine()->getRepository(Matches::class)->findBy(
                            ['team2' => $team->getTeamId()]
                        );

                        if (count($matchest1) == 0 || count($matchest2) == 0) {
                            $nbr = $team->getFfBought() + 1;

                            $team->setFfBought($nbr);

                            $tv = $team->getTv() + 10000;

                            $team->setTv($tv);

                            $inducost = 10000;

                            $treasury = $team->getTreasury() - 10000;

                            $team->setTreasury($treasury);
                        }
                    }

                    break;

                case "ac":
                    if ($team->getTreasury() >= 10000) {
                        $nbr = $team->getAssCoaches() + 1;

                        $team->setAssCoaches($nbr);

                        $tv = $team->getTv() + 10000;

                        $team->setTv($tv);

                        $inducost = 10000;

                        $treasury = $team->getTreasury() - 10000;

                        $team->setTreasury($treasury);
                    }

                    break;

                case "chl":
                    if ($team->getTreasury() >= 10000) {
                        $nbr = $team->getCheerleaders() + 1;

                        $team->setCheerleaders($nbr);

                        $tv = $team->getTv() + 10000;

                        $team->setTv($tv);

                        $inducost = 10000;

                        $treasury = $team->getTreasury() - 10000;

                        $team->setTreasury($treasury);
                    }

                    break;

                case "apo":
                    if ($team->getTreasury() >= 50000 && $team->getApothecary() < 1) {
                        $nbr = $team->getApothecary() + 1;

                        $team->setApothecary($nbr);

                        $tv = $team->getTv() + 50000;

                        $team->setTv($tv);

                        $inducost = 50000;

                        $treasury = $team->getTreasury() - 50000;

                        $team->setTreasury($treasury);
                    }

                    break;
            }

            $encoders = array(new XmlEncoder(), new JsonEncoder());
            $normalizers = array(new ObjectNormalizer());

            $serializer = new Serializer($normalizers, $encoders);

            $response = array(
                "tv" => $tv,
                "ptv" => ($tv / 1000),
                "tresor" => $team->getTreasury(),
                "inducost" => $inducost,
                "type" => $type,
                "nbr" => $nbr,
            );

            $entityManager->persist($team);

            $entityManager->flush();

            $jsonContent = $serializer->serialize($response, 'json');

            return new JsonResponse($jsonContent);
        }
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize(["rien"=>''], 'json');

        return new JsonResponse($jsonContent);
    }

    /**
     * @Route("/remInducement/{teamId}/{type}", options = { "expose" = true })
     * @param int $teamId
     * @param string $type
     * @return JsonResponse
     */
    public function remInducement($teamId, $type)
    {
        $team = $this->getDoctrine()->getRepository(Teams::class)->findOneBy(['teamId' => $teamId]);

        $nbr = 0;
        $inducost = 0;
        $tv = 0;

        if ($team) {
            $entityManager = $this->getDoctrine()->getManager();

            $matchest1 = $this->getDoctrine()->getRepository(Matches::class)->findBy(['team1' => $team->getTeamId()]);

            $matchest2 = $this->getDoctrine()->getRepository(Matches::class)->findBy(['team2' => $team->getTeamId()]);

            switch ($type) {
                case "rr":
                    if ($team->getRerolls() > 0) {
                        $race = $team->getFRace();

                        if ($race) {
                            $costRr = $race->getCostRr();
                        } else {
                            $costRr = 0;
                        }

                        $inducost = $costRr;

                        $nbr = $team->getRerolls() - 1;

                        $team->setRerolls($nbr);

                        if (count($matchest1) == 0 || count($matchest2) == 0) {
                            $treasury = $team->getTreasury() + $inducost;

                            $team->setTreasury($treasury);
                        }

                        $tv = $team->getTv() - $costRr;

                        $team->setTv($tv);
                    }

                    break;

                case "pop":
                    if ((count($matchest1) == 0 || count($matchest2) == 0) && $team->getFfBought() > 0) {
                        $nbr = $team->getFfBought() - 1;

                        $team->setFfBought($nbr);

                        $tv = $team->getTv() - 10000;

                        $team->setTv($tv);

                        $inducost = 10000;

                        if (count($matchest1) == 0 || count($matchest2) == 0) {
                            $treasury = $team->getTreasury() + 10000;

                            $team->setTreasury($treasury);
                        }
                    }


                    break;

                case "ac":
                    if ($team->getAssCoaches() > 0) {
                        $nbr = $team->getAssCoaches() - 1;

                        $team->setAssCoaches($nbr);

                        $tv = $team->getTv() - 10000;

                        $team->setTv($tv);

                        $inducost = 10000;

                        if (count($matchest1) == 0 || count($matchest2) == 0) {
                            $treasury = $team->getTreasury() + 10000;

                            $team->setTreasury($treasury);
                        }
                    }

                    break;

                case "chl":
                    if ($team->getCheerleaders() > 0) {
                        $nbr = $team->getCheerleaders() - 1;

                        $team->setCheerleaders($nbr);

                        $tv = $team->getTv() - 10000;

                        $team->setTv($tv);

                        $inducost = 10000;

                        if (count($matchest1) == 0 || count($matchest2) == 0) {
                            $treasury = $team->getTreasury() + 10000;

                            $team->setTreasury($treasury);
                        }
                    }

                    break;

                case "apo":
                    if ($team->getApothecary() > 0) {
                        $nbr = $team->getApothecary() - 1;

                        $team->setApothecary($nbr);

                        $tv = $team->getTv() - 50000;

                        $team->setTv($tv);

                        $inducost = 50000;

                        if (count($matchest1) == 0 || count($matchest2) == 0) {
                            $treasury = $team->getTreasury() + 50000;

                            $team->setTreasury($treasury);
                        }
                    }
                    break;
            }

            $encoders = array(new XmlEncoder(), new JsonEncoder());
            $normalizers = array(new ObjectNormalizer());

            $serializer = new Serializer($normalizers, $encoders);

            $response = [
                "tv" => $tv,
                "ptv" => ($tv / 1000),
                "tresor" => $team->getTreasury(),
                "inducost" => $inducost,
                "type" => $type,
                "nbr" => $nbr,
            ];

            $entityManager->persist($team);

            $entityManager->flush();

            $jsonContent = $serializer->serialize($response, 'json');

            return new JsonResponse($jsonContent);
        }
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize(["rien"=>''], 'json');

        return new JsonResponse($jsonContent);
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

            $encoders = array(new XmlEncoder(), new JsonEncoder());
            $normalizers = array(new ObjectNormalizer());

            $serializer = new Serializer($normalizers, $encoders);

            $response = array();

            $entityManager->persist($team);

            $entityManager->flush();

            $jsonContent = $serializer->serialize($response, 'json');

            return new JsonResponse($jsonContent);
        }
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize(["rien"=>''], 'json');

        return new JsonResponse($jsonContent);
    }

    /**
     * @Route("/dropdownTeams/{nbr}")
     * @param int $nbr
     * @return Response
     */

    public function dropdownTeams($nbr)
    {
        $setting = $this->getDoctrine()->getRepository(Setting::class)->findOneBy(['name' => 'year']);

        if ($setting) {
            $teams = $this->getDoctrine()->getRepository(Teams::class)->findBy(
                ['retired' => 0, 'year' => $setting->getValue()],
                ['name' => 'ASC']
            );

            return $this->render('statbb/dropdownteams.html.twig', ['teams' => $teams, 'nbr' => $nbr]);
        }
        return $this->render('statbb/base.html.twig');
    }

    /**
     * @Route("/dropdownPlayer/{teamId}/{nbr}", options = { "expose" = true })
     * @param int $teamId
     * @param int $nbr
     * @return JsonResponse
     */

    public function dropdownPlayer($teamId, $nbr)
    {
        $players = $this->getDoctrine()->getRepository(Players::class)->findBy(
            ['ownedByTeam' => $teamId],
            ['nr' => 'ASC']
        );

        foreach ($players as $number => $player) {
            if ($player->getStatus() == 7 || $player->getStatus() == 8 || $player->getInjRpm() == 1) {
                unset($players[$number]);
            }
        }

        $response = [
            'html' => $this->renderView(
                'statbb/dropdownplayers.html.twig',
                ['players' => $players, 'teamId' => $teamId, 'nbr' => $nbr]
            ),
        ];

        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        $serializer = new Serializer($normalizers, $encoders);

        $jsonContent = $serializer->serialize($response, 'json');

        return new JsonResponse($jsonContent);
    }

    /**
     * @Route("/addGame", options = { "expose" = true })
     * @param Request $request
     * @return JsonResponse
     */

    public function addGame(Request $request)
    {

        $parametersAsArray = [];

        if ($content = $request->getContent()) {
            $parametersAsArray = json_decode($content, true);
        }

        $entityManager = $this->getDoctrine()->getManager();

        $match = new Matches();

        $team1 = $this->getDoctrine()->getRepository(Teams::class)->findOneBy(
            ['teamId' => $parametersAsArray['team_1']]
        );
        $team2 = $this->getDoctrine()->getRepository(Teams::class)->findOneBy(
            ['teamId' => $parametersAsArray['team_2']]
        );

        if ($team1 && $team2) {
            $dateCreatedFormat = DateTime::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s"));
            if ($dateCreatedFormat) {
                $match->setDateCreated($dateCreatedFormat);
            }
            $match->setFans($parametersAsArray['totalpop']);
            $match->setFfactor1($parametersAsArray['varpop_team1']);
            $match->setFfactor2($parametersAsArray['varpop_team2']);
            $match->setIncome1($parametersAsArray['gain1']);
            $match->setIncome2($parametersAsArray['gain2']);
            $match->setTeam1Score($parametersAsArray['score1']);
            $match->setTeam2Score($parametersAsArray['score2']);
            $match->setTeam1($team1);
            $match->setTeam2($team2);
            $match->setTv1((int)$team1->getTv());
            $match->setTv2((int)$team2->getTv());

            $entityManager->persist($match);

            $players = $this->getDoctrine()->getRepository(Players::class)->findBy(
                ['ownedByTeam' => $team1->getTeamId()]
            );

            foreach ($players as $player) {
                if ($player->getStatus() == 7 || $player->getStatus() == 8 || $player->getInjRpm() == 1) {
                } else {
                    $matchdata = new MatchData();

                    $matchdata->setAgg(0);
                    $matchdata->setBh(0);
                    $matchdata->setCp(0);
                    $matchdata->setFMatch($match);
                    $matchdata->setFPlayer($player);
                    $matchdata->setInj(0);
                    $matchdata->setIntcpt(0);
                    $matchdata->setKi(0);
                    $matchdata->setMvp(0);
                    $matchdata->setSi(0);
                    $matchdata->setTd(0);

                    $player->setInjRpm(0);

                    $entityManager->persist($matchdata);

                    $entityManager->flush();
                }
            }

            foreach ($players as $player) {
                if ($player->getStatus() == 7 || $player->getStatus() == 8) {
                } elseif ($player->getInjRpm() == 1) {
                    $matchdata = new MatchData();

                    $player->setInjRpm(0);

                    $entityManager->persist($matchdata);

                    $entityManager->flush();
                }
            }

            $players = $this->getDoctrine()->getRepository(Players::class)->findBy(
                ['ownedByTeam' => $team2->getTeamId()]
            );

            foreach ($players as $player) {
                if ($player->getStatus() == 7 || $player->getStatus() == 8 || $player->getInjRpm() == 1) {
                } else {
                    $matchdata = new MatchData();

                    $matchdata->setAgg(0);
                    $matchdata->setBh(0);
                    $matchdata->setCp(0);
                    $matchdata->setFMatch($match);
                    $matchdata->setFPlayer($player);
                    $matchdata->setInj(0);
                    $matchdata->setIntcpt(0);
                    $matchdata->setKi(0);
                    $matchdata->setMvp(0);
                    $matchdata->setSi(0);
                    $matchdata->setTd(0);

                    $player->setInjRpm(0);

                    $entityManager->persist($matchdata);

                    $entityManager->flush();
                }
            }

            foreach ($players as $player) {
                if ($player->getStatus() == 7 || $player->getStatus() == 8) {
                } elseif ($player->getInjRpm() == 1) {
                    $matchdata = new MatchData();

                    $player->setInjRpm(0);

                    $entityManager->persist($matchdata);

                    $entityManager->flush();
                }
            }

            $team1->setTreasury($team1->getTreasury() + $parametersAsArray['gain1']);
            $team2->setTreasury($team2->getTreasury() + $parametersAsArray['gain2']);

            if ($team1->getFf() + $parametersAsArray['varpop_team1'] < 0) {
                $team1->setFf(0);
            } else {
                $team1->setFf($team1->getFf() + $parametersAsArray['varpop_team1']);
            }

            if ($team2->getFf() + $parametersAsArray['varpop_team2'] < 0) {
                $team2->setFf(0);
            } else {
                $team2->setFf($team2->getFf() + $parametersAsArray['varpop_team2']);
            }

            $entityManager->persist($team1);
            $entityManager->persist($team2);

            $entityManager->flush();

            /*$tt = $this->getDoctrine()->getRepository(MatchData::class)->findOneBy(
                ['fPlayer' => $parametersAsArray['player']['0']['id'], 'fMatch' => $match->getMatchId()]
            );*/

            foreach ($parametersAsArray['player'] as $action) {
                $mdplayer = $this->getDoctrine()->getRepository(MatchData::class)->findOneBy(
                    ['fPlayer' => $action['id'], 'fMatch' => $match->getMatchId()]
                );
                $playerstat = $this->getDoctrine()->getRepository(Players::class)->findOneBy(
                    ['playerId' => $action['id']]
                );

                if ($mdplayer && $playerstat) {
                    switch ($action['action']) {
                        case 'COMP':
                            $mdplayer->setCp(1);
                            break;
                        case 'TD':
                            $mdplayer->setTd(1);
                            break;
                        case 'INT':
                            $mdplayer->setIntcpt(1);
                            break;
                        case 'CAS - BH':
                            $mdplayer->setBh(1);
                            break;
                        case 'CAS - SI':
                            $mdplayer->setSi(1);
                            break;
                        case 'CAS - KI':
                            $mdplayer->setKi(1);
                            break;
                        case 'MVP':
                            $mdplayer->setMvp(1);
                            break;
                        case 'AGG':
                            $mdplayer->setAgg(1);
                            break;
                        case '-1 Ma':
                            $playerstat->setInjMa($playerstat->getInjMa() + 1);
                            $playerstat->setInjRpm(1);
                            break;
                        case '-1 St':
                            $playerstat->setInjSt($playerstat->getInjSt() + 1);
                            $playerstat->setInjRpm(1);
                            break;
                        case '-1 Ag':
                            $playerstat->setInjAg($playerstat->getInjAg() + 1);
                            $playerstat->setInjRpm(1);
                            break;
                        case '-1 Av':
                            $playerstat->setInjAv($playerstat->getInjAv() + 1);
                            $playerstat->setInjRpm(1);
                            break;
                        case 'Ni':
                            $playerstat->setInjNi($playerstat->getInjNi() + 1);
                            $playerstat->setInjRpm(1);
                            break;
                        case 'RPM':
                            $playerstat->setInjRpm(1);
                            break;
                        case 'Tué':
                            $dateDiedFormat = DateTime::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s"));
                            if ($dateDiedFormat) {
                                $playerstat->setDateDied($dateDiedFormat);
                            }
                            $playerstat->setStatus(8);
                            break;
                    }

                    $entityManager->persist($mdplayer);

                    $mdata = $this->getDoctrine()->getRepository(MatchData::class)->findBy(
                        ['fPlayer' => $playerstat->getPlayerId()]
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

                    $nbrskill = $this->getDoctrine()->getRepository(PlayersSkills::class)->findBy(
                        ['fPid' => $playerstat->getPlayerId()]
                    );

                    switch (count($nbrskill)) {
                        case 0:
                            if ($spp > 5) {
                                $playerstat->setStatus(9);
                            }
                            break;
                        case 1:
                            if ($spp > 15) {
                                $playerstat->setStatus(9);
                            }
                            break;
                        case 2:
                            if ($spp > 30) {
                                $playerstat->setStatus(9);
                            }
                            break;
                        case 3:
                            if ($spp > 50) {
                                $playerstat->setStatus(9);
                            }
                            break;
                        case 4:
                            if ($spp > 75) {
                                $playerstat->setStatus(9);
                            }
                            break;
                        case 5:
                            if ($spp > 175) {
                                $playerstat->setStatus(9);
                            }
                            break;
                    }
                    $entityManager->persist($playerstat);
                }
            }

            $team1->setTv((int)$this->calculateTV($team1) * 1000);
            $team2->setTv((int)$this->calculateTV($team2) * 1000);

            $team1->setRdy(false);
            $team2->setRdy(false);

            $entityManager->persist($team1);
            $entityManager->persist($team2);

            $entityManager->flush();

            $tt = 'ok';

            $encoders = array(new XmlEncoder(), new JsonEncoder());
            $normalizers = array(new ObjectNormalizer());

            $serializer = new Serializer($normalizers, $encoders);

            $jsonContent = $serializer->serialize($tt, 'json');

            return new JsonResponse($jsonContent);
        } else {
            $tt = 'error';

            $encoders = array(new XmlEncoder(), new JsonEncoder());
            $normalizers = array(new ObjectNormalizer());

            $serializer = new Serializer($normalizers, $encoders);

            $jsonContent = $serializer->serialize($tt, 'json');

            return new JsonResponse($jsonContent);
        }
    }

    /**
     * @param Teams $team
     * @return float|int
     */
    public function calculateTV($team) //TODO passer ça dans un service....
    {
        $tcost = 0;

        $team = $this->getDoctrine()->getRepository(Teams::class)->find($team->getTeamId());

        if ($team) {
            $players = $this->getDoctrine()->getRepository(Players::class)->findBy(
                ['ownedByTeam' => $team->getTeamId()],
                ['nr' => 'ASC']
            );

            if ($players) {
                foreach ($players as $player) {
                    if ($player->getStatus() != 7 && $player->getStatus() != 8) {
                        $playerPosition = $player->getFPos();

                        $pcost = 0;

                        if ($playerPosition) {
                            $pcost += $playerPosition->getCost();
                        }

                        $supcomp = $this->getDoctrine()->getRepository(PlayersSkills::class)->findBy(
                            ['fPid' => $player->getPlayerId()]
                        );

                        foreach ($supcomp as $comps) {
                            if ($comps->getType() == 'N') {
                                $pcost += 20000;
                            } else {
                                $pcost += 30000;
                            }
                        }

                        if ($player->getAchMa() > 0) {
                            $pcost += 30000;
                        }

                        if ($player->getAchSt() > 0) {
                            $pcost += 50000;
                        }

                        if ($player->getAchAg() > 0) {
                            $pcost += 40000;
                        }

                        if ($player->getAchAv() > 0) {
                            $pcost += 30000;
                        }

                        $tcost += $pcost;
                    }
                }
            }

            $race = $team->getFRace();

            if ($race) {
                $costRr = $race->getCostRr();
            } else {
                $costRr = 0;
            }

            $tcost += $team->getRerolls() * $costRr;
            $tcost += ($team->getFf() + $team->getFfBought()) * 10000;
            $tcost += $team->getAssCoaches() * 10000;
            $tcost += $team->getCheerleaders() * 10000;
            $tcost += $team->getApothecary() * 50000;


            $tv = $tcost / 1000;

            return $tv;
        } else {
            return 9999;
        }
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

            $team->setTv((int)$this->calculateTV($team) * 1000);
            $team->setRdy(true);

            $entityManager->persist($team);
            $entityManager->flush();

            return $this->redirectToRoute('team', ['teamid' => $team->getTeamId(), 'type' => 'n'], 302);
        }
        return $this->redirectToRoute('index');
    }

    /**
     * @Route("/skillmodal/{playerid}", name="skillmodal", options = { "expose" = true })
     * @param int $playerid
     * @return Response
     */
    public function skillmodal($playerid)
    {
        $listskill = $this->getDoctrine()->getRepository(GameDataSkills::class)->findAll();

        foreach ($listskill as $key => $skill) {
            if ($skill->getCat() == 'E') {
                unset($listskill[$key]);
            }
        }
        return $this->render('statbb/skillmodal.html.twig', ['listskill' => $listskill, 'playerId' => $playerid]);
    }

    /**
     * @Route("/addComp/{skillid}/{playerid}", name="addComp", options = { "expose" = true })
     * @param int $skillid
     * @param int $playerid
     * @return Response
     */
    public function addComp($skillid, $playerid)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $skill = $this->getDoctrine()->getRepository(GameDataSkills::class)->findOneBy(['skillId' => $skillid]);

        $player = $this->getDoctrine()->getRepository(players::class)->findOneBy(['playerId' => $playerid]);

        if ($player && $skill) {
            $toadd = new PlayersSkills();

            $toadd->setFPid($player);

            $toadd->setFSkill($skill);

            $playerPosition = $player->getFPos();

            $norm ='';
            $double = '';

            if ($playerPosition) {
                $double = $playerPosition->getDoub();
                $norm = $playerPosition->getNorm();
            }

            $pos = stripos((string)$norm, (string) $skill->getCat());

            if ($pos === false) {
            } else {
                $toadd->setType('N');
            }

            $pos = stripos((string)$double, (string)$skill->getCat());

            if ($pos === false) {
            } else {
                $toadd->setType('D');
            }
            $entityManager->persist($toadd);
            $entityManager->flush();

            $player->setStatus(1);

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
     * @Route("/pdfteam/{id}", name="pdfteam", options = { "expose" = true })
     * @param int $id
     */
    public function pdfteam($id)
    {
        $players = $this->getDoctrine()->getRepository(Players::class)->findBy(
            ['ownedByTeam' => $id],
            ['nr' => 'ASC']
        );

        $team = $this->getDoctrine()->getRepository(Teams::class)->find($id);

        $allskills = $this->getDoctrine()->getRepository(GameDataSkills::class)->findAll();

        $count = 0;
        $tdata['playersCost'] = 0;

        $pdata = [];
        $pdata[]=[];

        if ($players) {
            foreach ($players as $number => $player) {
                $tcost = 0;

                $playerListSkills = '';
                $playerPosition = $player->getFPos();

                if ($playerPosition) {
                    $playerListSkills = $playerPosition->getSkills();
                }

                $playerskills = explode(",", (string)$playerListSkills);

                $listskill = '';

                foreach ($playerskills as $playerskill) {
                    foreach ($allskills as $baseskill) {
                        if ($baseskill->getSkillId() == $playerskill) {
                            $listskill .= '<text class="test-primary">'.$baseskill->getName().'</text>, ';
                        }
                    }
                }

                if ($listskill == '<text class="test-primary"></text>, ') {
                    $listskill = '';
                }

                $supcomp = $this->getDoctrine()->getRepository(PlayersSkills::class)->findBy(
                    ['fPid' => $player->getPlayerId()]
                );

                if ($supcomp) {
                    foreach ($supcomp as $comps) {
                        $compsDet = $comps->getFSkill();
                        $compsName = '';

                        if ($compsDet) {
                            $compsName = $compsDet->getName();
                        }
                        if ($comps->getType() == 'N') {
                            $tcost += 20000;
                            $listskill .= '<text class="text-success">'.$compsName.'</text>, ';
                        } else {
                            $tcost += 30000;
                            $listskill .= '<text class="text-danger">'.$compsName.'</text>, ';
                        }
                    }
                }

                if ($player->getInjNi() > 0) {
                    $listskill .= '<text class="text-danger">+1 Ni</text>, ';
                }

                if ($player->getAchMa() > 0) {
                    $listskill .= '<text class="text-success">+1 Ma</text>, ';

                    $tcost += 30000;
                }

                if ($player->getAchSt() > 0) {
                    $listskill .= '<text class="text-success">+1 St</text>, ';

                    $tcost += 50000;
                }

                if ($player->getAchAg() > 0) {
                    $listskill .= '<text class="text-success">+1 Ag</text>, ';

                    $tcost += 40000;
                }

                if ($player->getAchAv() > 0) {
                    $listskill .= '<text class="text-success">+1 Av</text>, ';

                    $tcost += 30000;
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

                $playerBaseCost = 0;

                if ($playerPosition) {
                    $playerBaseCost = $playerPosition->getCost();
                }
                $pdata[$count]['pid'] = $player->getPlayerId();
                $pdata[$count]['nbrm'] = count($mdata);
                $pdata[$count]['cp'] = $tcp;
                $pdata[$count]['td'] = $ttd;
                $pdata[$count]['int'] = $tint;
                $pdata[$count]['cas'] = $tcas;
                $pdata[$count]['mvp'] = $tmvp;
                $pdata[$count]['agg'] = $tagg;
                $pdata[$count]['skill'] = substr($listskill, 0, strlen($listskill) - 2);
                $pdata[$count]['spp'] = $tcp + ($ttd * 3) + ($tint * 2) + ($tcas * 2) + ($tmvp * 5);
                $pdata[$count]['cost'] = $playerBaseCost + $tcost;

                switch ($player->getStatus()) {
                    case 7:
                        $pdata[$count]['status'] = 'VENDU';
                        break;
                    case 8:
                        $pdata[$count]['status'] = 'MORT';
                        break;
                    case 9:
                        $pdata[$count]['status'] = 'PX';
                        $tdata['playersCost'] += $pdata[$count]['cost'];
                        break;
                    default:
                        if ($player->getInjRpm() != 0) {
                            $pdata[$count]['status'] = 'RPM';
                            $pdata[$count]['cost'] = '<s>'.$pdata[$count]['cost'].'</s>';
                        } else {
                            $pdata[$count]['status'] = '';
                            $tdata['playersCost'] += $pdata[$count]['cost'];
                        }
                        break;
                }

                if (!$player->getName()) {
                    $player->setName('Inconnu');
                }

                $count++;
            }
        }
        if ($team) {
            $race = $team->getFRace();

            if ($race) {
                $costRr = $race->getCostRr();
            } else {
                $costRr = 0;
            }

            $tdata['rerolls'] = $team->getRerolls() * $costRr;
            $tdata['pop'] = ($team->getFf() + $team->getFfBought()) * 10000;
            $tdata['asscoaches'] = $team->getAssCoaches() * 10000;
            $tdata['cheerleader'] = $team->getCheerleaders() * 10000;
            $tdata['apo'] = $team->getApothecary() * 50000;
            $tdata['tv'] = $tdata['playersCost'] + $tdata['rerolls']
                + $tdata['pop'] + $tdata['asscoaches'] + $tdata['cheerleader'] + $tdata['apo'];
        }

        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->setIsRemoteEnabled(true);
        $pdfOptions->setIsHtml5ParserEnabled(true);

        $dompdf = new Dompdf($pdfOptions);

        $html = $this->renderView(
            'statbb/pdfteam.html.twig',
            [
                'players' => $players,
                'team' => $team,
                'pdata' => $pdata,
                'tdata' => $tdata,
            ]
        );

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'landscape');

        // Render the HTML as PDF
        $dompdf->render();

        if ($team) {
            $dompdf->stream($team->getName(). '.pdf', ["Attachment" => true]);
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
    public function ajoutMatch()
    {
        return $this->render('statbb/ajoutMatch.html.twig');
    }
}
