<?php

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

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;


class StatBBController extends Controller
{
    /**
     * @Route("/showteams", name="showteams", options = { "expose" = true })
     */
    public function show_teams()
    {

        $setting = $this->getDoctrine()->getRepository(Setting::class)->findOneBy(['name' => 'year']);

        $teams = $this->getDoctrine()->getRepository(Teams::class)->findBy(['year'=> $setting->getValue(),'retired'=>false], array('name' => 'ASC'));

        foreach ($teams as $number => $team) {

            if ($team->getYear() < 2 || $team->getRetired() == true) {
                unset($teams[$number]);
            } else {

                $team->setTv($team->getTv());
            }


        }

        return $this->render('statbb/showteams.html.twig', ['teams' => $teams]);

    }

    /**
     * @Route("/showuserteams", name="showuserteams", options = { "expose" = true })
     */
    public function show_user_teams()
    {
        $setting = $this->getDoctrine()->getRepository(Setting::class)->findOneBy(['name' => 'year']);

        $coachTeam = $this->getDoctrine()->getRepository(Teams::class)->findBy(
            ['ownedByCoach' => $this->getUser(), 'year' => $setting->getValue(),'retired'=>false]
        );

        $count = 0;

        foreach ($coachTeam as $number => $team) {

            $matches1 = $this->getDoctrine()->getRepository(Matches::class)->findBy(
                array('team1' => $team->getTeamId()),
                array('dateCreated' => 'DESC')
            );
            $matches2 = $this->getDoctrine()->getRepository(Matches::class)->findBy(
                array('team2' => $team->getTeamId()),
                array('dateCreated' => 'DESC')
            );

            $matches = array_merge($matches1, $matches2);

            $win = 0;
            $draw = 0;
            $loss = 0;

            foreach ($matches as $match) {
                if (($team == $match->getTeam1() && $match->getTeam1Score() > $match->getTeam2Score(
                        )) || ($team == $match->getTeam2() && $match->getTeam1Score() < $match->getTeam2Score())) {
                    $win++;
                } elseif (($team == $match->getTeam1() && $match->getTeam1Score() < $match->getTeam2Score(
                        )) || ($team == $match->getTeam2() && $match->getTeam1Score() > $match->getTeam2Score())) {
                    $loss++;
                } elseif (($team == $match->getTeam1() && $match->getTeam1Score() == $match->getTeam2Score(
                        )) || ($team == $match->getTeam2() && $match->getTeam1Score() == $match->getTeam2Score())) {
                    $draw++;
                }
            }

            $tdata[$count]['tId'] = $team->getTeamId();
            $tdata[$count]['win'] = $win;
            $tdata[$count]['loss'] = $loss;
            $tdata[$count]['draw'] = $draw;

            $count++;

        }


        return $this->render('statbb/user_teams.html.twig', ['coachteam' => $coachTeam, 'tdata' => $tdata]);
    }

    /**
     * @Route("/team/{id}/{type}", name="team", options = { "expose" = true })
     */
    public function show_team($id, $type)
    {
        $user = $this->getUser();

        if (!isset($lastUsername)) {

            $lastUsername = '';

        }

        $players = $this->getDoctrine()->getRepository(Players::class)->findBy(
            array('ownedByTeam' => $id),
            array('nr' => 'ASC')
        );

        $team = $this->getDoctrine()->getRepository(Teams::class)->find($id);

        $allskills = $this->getDoctrine()->getRepository(GameDataSkills::class)->findAll();

        $count = 0;
        $tdata['playersCost'] = 0;

        if (empty($players)) {
            $pdata = '';
        } else {

        }

        foreach ($players as $number => $player) {

            $tcost = 0;

            $playerskills = explode(",", $player->getFPos()->getSkills());

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

            foreach ($supcomp as $comps) {


                if ($comps->getType() == 'N') {

                    $tcost += 20000;
                    $listskill .= '<text class="text-success">'.$comps->getFSkill()->getName().'</text>, ';

                } else {

                    $tcost += 30000;
                    $listskill .= '<text class="text-danger">'.$comps->getFSkill()->getName().'</text>, ';

                }

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
            $pdata[$count]['cost'] = $player->getFPos()->getCost() + $tcost;

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

        $tdata['rerolls'] = $team->getRerolls() * $team->getFRace()->getCostRr();
        $tdata['pop'] = ($team->getFf() + $team->getFfBought()) * 10000;
        $tdata['asscoaches'] = $team->getAssCoaches() * 10000;
        $tdata['cheerleader'] = $team->getCheerleaders() * 10000;
        $tdata['apo'] = $team->getApothecary() * 50000;
        $tdata['tv'] = $tdata['playersCost'] + $tdata['rerolls'] + $tdata['pop'] + $tdata['asscoaches'] + $tdata['cheerleader'] + $tdata['apo'];


        if ($type == "modal") {
            return $this->render(
                'statbb/team_modal.html.twig',
                array('players' => $players, 'team' => $team, 'pdata' => $pdata, 'tdata' => $tdata)
            );
        } else {
            return $this->render(
                'statbb/team.html.twig',
                array(
                    'players' => $players,
                    'last_username' => $lastUsername,
                    'team' => $team,
                    'pdata' => $pdata,
                    'tdata' => $tdata,
                )
            );
        }

    }

    /**
     * @Route("/player/{id}/{type}", name="Player", options = { "expose" = true })
     */
    public function show_player($id, $type)
    {

        if (!isset($lastUsername)) {

            $lastUsername = '';

        }

        $player = $this->getDoctrine()->getRepository(Players::class)->findOneBy(['playerId' => $id]);

        $allskills = $this->getDoctrine()->getRepository(GameDataSkills::class)->findAll();

        $tcost = 0;

        $playerskills = explode(",", $player->getFPos()->getSkills());

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

        foreach ($supcomp as $comps) {


            if ($comps->getType() == 'N') {

                $tcost += 20000;
                $listskill .= '<text class="text-success">'.$comps->getFSkill()->getName().'</text>, ';

            } else {

                $tcost += 30000;
                $listskill .= '<text class="text-danger">'.$comps->getFSkill()->getName().'</text>, ';

            }

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

        $mdata = $this->getDoctrine()->getRepository(MatchData::class)->findBy(['fPlayer' => $player->getPlayerId()]);

        $tcp = 0;
        $ttd = 0;
        $tint = 0;
        $tcas = 0;
        $tmvp = 0;
        $tagg = 0;

        $matches = array();

        $count = 0;

        foreach ($mdata as $game) {

            $tcp += $game->getCp();
            $ttd += $game->getTd();
            $tint += $game->getIntcpt();
            $tcas += ($game->getBh() + $game->getSi() + $game->getKi());
            $tmvp += $game->getMvp();
            $tagg += $game->getAgg();

            $matches[] = $game->getFMatch()->getMatchId();

            if ($game->getCp() > 0 || $game->getTd() > 0 || $game->getIntcpt() > 0 || ($game->getBh() + $game->getSi(
                    ) + $game->getKi()) > 0 || $game->getMvp() > 0 || $game->getAgg() > 0) {

                $rec = '';

                if ($game->getCp() > 0) {
                    $rec .= 'CP: '.$game->getCp().', ';
                }

                if ($game->getTd() > 0) {
                    $rec .= 'TD: '.$game->getTd().', ';
                }

                if ($game->getIntcpt() > 0) {
                    $rec .= 'INT: '.$game->getIntcpt().',';
                }

                if (($game->getBh() + $game->getSi() + $game->getKi()) > 0) {
                    $rec .= 'CAS: '.($game->getBh() + $game->getSi() + $game->getKi()).', ';
                }

                if ($game->getMvp() > 0) {
                    $rec .= 'MVP: '.$game->getMvp().', ';
                }

                if ($game->getAgg() > 0) {
                    $rec .= 'AGG: '.$game->getAgg().', ';
                }

                $msdata[$count]["mId"] = $game->getFMatch()->getMatchId();
                $msdata[$count]["data"] = substr($rec, 0, strlen($rec) - 2);

                $count++;
            }

        }

        if ($count == 0) {
            $msdata[$count]["mId"] = 0;
            $msdata[$count]["data"] = '';
        }

        $pdata['nbrm'] = count($mdata);
        $pdata['cp'] = $tcp;
        $pdata['td'] = $ttd;
        $pdata['int'] = $tint;
        $pdata['cas'] = $tcas;
        $pdata['mvp'] = $tmvp;
        $pdata['agg'] = $tagg;
        $pdata['skill'] = substr($listskill, 0, strlen($listskill) - 2);
        $pdata['spp'] = $tcp + ($ttd * 3) + ($tint * 2) + ($tcas * 2) + ($tmvp * 5);
        $pdata['cost'] = $player->getFPos()->getCost() + $tcost;

        if (!$player->getName()) {
            $player->setName('Inconnu');
        }

        $matches = $this->getDoctrine()->getRepository(Matches::class)->findBy(array('matchId' => $matches));

        if ($type == "modal") {
            return $this->render(
                'statbb/player_modal.html.twig',
                array(
                    'player' => $player,
                    'pdata' => $pdata,
                    'matches' => $matches,
                    'mdata' => $msdata,
                    'last_username' => $lastUsername,
                )
            );
        } else {
            return $this->render(
                'statbb/player.html.twig',
                array(
                    'player' => $player,
                    'pdata' => $pdata,
                    'matches' => $matches,
                    'mdata' => $msdata,
                    'last_username' => $lastUsername,
                )
            );
        }

    }


    /**
     * @Route("/", name="index", options = { "expose" = true })
     */
    public function index(AuthenticationUtils $authenticationUtils)
    {
        $user = $this->getUser();

        if (isset($user)) {

            return $this->render('statbb/user.html.twig');

        } else {

            return $this->render('statbb/front.html.twig');

        }


    }

    /**
     * @Route("/admin")
     */
    public function admin()
    {
        return new Response('<html><body>Admin page!</body></html>');
    }

    /**
     * @Route("/login", name="login", options = { "expose" = true })
     */
    public function login(Request $request, AuthenticationUtils $authenticationUtils)
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        /*return $this->render('statbb/login.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
        ));
        */

        return $this->render('statbb/front.html.twig');
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout(Request $request, AuthenticationUtils $authenticationUtils)
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
     * @Route("/classement/general/{limit}", name="classementgen", options = { "expose" = true })
     */
    public function class_gen($limit)
    {
        $setting = $this->getDoctrine()->getRepository(Setting::class)->findOneBy(['name' => 'year']);

        $classement = $this->getDoctrine()->getRepository(Teams::class)->classement($setting->getValue(), $limit);

        return $this->render('statbb/classement.html.twig', ['classement' => $classement, 'limit' => $limit]);
    }


    /**
     * @Route("/classement/{type}/{teamorplayer}/{limit}", name="classement", options = { "expose" = true })
     */
    public function Sclass($type, $teamorplayer, $limit)
    {
        $setting = $this->getDoctrine()->getRepository(Setting::class)->findOneBy(['name' => 'year']);

        $matches_data = $this->getDoctrine()->getRepository(MatchData::class)->Sclassement(
            $setting->getValue(),
            $type,
            $teamorplayer,
            $limit
        );


        if ($teamorplayer == 'player') {

            switch ($type) {
                case 'bash':

                    $title = 'The Bash Lord - Most CAS';
                    $class = 'class_bash';

                    break;

                case 'td':

                    $title = 'The Golden Hand - Most TD';
                    $class = 'class_td';

                    break;

                case 'xp':

                    $title = 'The Best - Most SPP';
                    $class = 'class_xp';

                    break;

                case 'pass':

                    $title = 'The Ball Giver - Most Pass';
                    $class = 'class_pass';

                    break;

                case 'foul' :

                    $title = 'The Cheater - Most Foul';
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

                    $title = 'The most cas team';
                    $class = 'class_Tbash';

                    break;

                case 'td':

                    $title = 'The most Td team';
                    $class = 'class_Ttd';

                    break;


                case 'dead' :

                    $title = 'The most deads team';
                    $class = 'class_Tdead';
                    break;

                case 'foul' :

                    $title = 'The most foul team';
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

    /**
     * @Route("/totalcas", options = { "expose" = true })
     */
    public function Totalcas()
    {
        $setting = $this->getDoctrine()->getRepository(Setting::class)->findOneBy(['name' => 'year']);

        $total_cas = $this->getDoctrine()->getRepository(MatchData::class)->totalcas($setting->getValue());

        $matches = $this->getDoctrine()->getRepository(Matches::class)->findAll();

        foreach ($matches as $number => $match) {
            if ($match->getTeam1()->getYear() != $setting->getValue() || $match->getTeam2()->getYear(
                ) != $setting->getValue()) {
                unset($matches[$number]);

            }

        }

        return new Response(
            '<strong>Total : '.$total_cas[0]['score'].' In '.count($matches).' games</strong><br/>
			 <strong>By Game :  '.round($total_cas[0]['score'] / count($matches), 2).'</strong>'
        );

    }


    /**
     * @Route("/lastfive", options = { "expose" = true })
     */
    public function lastfive()
    {
        $setting = $this->getDoctrine()->getRepository(Setting::class)->findOneBy(['name' => 'year']);

        $matches = $this->getDoctrine()->getRepository(Matches::class)->findBy(array(), array('dateCreated' => 'DESC'));

        foreach ($matches as $number => $match) {
            if ($match->getTeam1()->getYear() != $setting->getValue() || $match->getTeam2()->getYear(
                ) != $setting->getValue()) {
                unset($matches[$number]);

            }

        }

        for ($x = 0; $x < 5; $x++) {
            $games[$x] = $matches[$x];
        }

        return $this->render('statbb/lastfivesmatches.html.twig', array('games' => $games));

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
     */
    public function getposstat($posId)
    {

        $position = $this->getDoctrine()->getRepository(GameDataPlayers::class)->findOneBy(['posId' => $posId]);

        $allskills = $this->getDoctrine()->getRepository(GameDataSkills::class)->findAll();

        $playerskills = explode(",", $position->getSkills());

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

    /**
     * @Route("/raceSelector", options = { "expose" = true })
     */
    public function raceSelector()
    {

        $races = $this->getDoctrine()->getRepository(races::class)->findAll();

        return $this->render('statbb/addteam.html.twig', ['races' => $races]);
    }

    /**
     * @Route("/createTeam/{teamname}/{coachid}/{raceid}", options = { "expose" = true })
     */
    public function create_team($teamname, $coachid, $raceid)
    {
        $setting = $this->getDoctrine()->getRepository(Setting::class)->findOneBy(['name' => 'year']);

        $entityManager = $this->getDoctrine()->getManager();

        $team = new Teams();

        $team->setName($teamname);

        $team->setFRace($this->getDoctrine()->getRepository(races::class)->findOneBy(array('raceId' => $raceid)));

        $team->setOwnedByCoach(
            $this->getDoctrine()->getRepository(Coaches::class)->findOneBy(array('coachId' => $coachid))
        );

        $team->setYear($setting->getValue());

        $team->setTreasury(1000000);

        $entityManager->persist($team);

        $entityManager->flush();

        $response = new Response();
        $response->setContent($team->getTeamId());
        $response->setStatusCode(200);

        return $response;
    }

    /**
     * @Route("/tk", name="tk")
     */
    public function tk()
    {
        return '<div class="panel"><h1>test</h1></div>';
    }

    /**
     * @Route("/player_adder/{raceId}/{teamId}", name="player_adder", options = { "expose" = true })
     */
    public function player_adder($raceId, $teamId)
    {

        $race = $this->getDoctrine()->getRepository(races::class)->findOneBy(array('raceId' => $raceId));

        $playerpositions = $this->getDoctrine()->getRepository(GameDataPlayers::class)->findBy(['fRace' => $race]);

        return $this->render(
            'statbb/playeradder.html.twig',
            ['playerpositions' => $playerpositions, 'teamId' => $teamId]
        );
    }

    /**
     * @Route("/add_player/{posId}/{teamId}", options = { "expose" = true })
     */
    public function add_player($posId, $teamId)
    {

        $position = $this->getDoctrine()->getRepository(GameDataPlayers::class)->findOneBy(['posId' => $posId]);

        $team = $this->getDoctrine()->getRepository(Teams::class)->findOneBy(['teamId' => $teamId]);

        $tv = 0;
        $tresors = '';
        $pcost = '';
        $reponse = '';
        $html = '';

        $player = new Players();

        $listskill = '';

        if ($team->getTreasury() > $position->getCost()) {
            $allPlayerByPos = $this->getDoctrine()->getRepository(Players::class)->findBy(
                ['fPos' => $position, 'ownedByTeam' => $team]
            );

            $count = count($allPlayerByPos);

            if ($count < $position->getQty()) {

                $tresors = $team->getTreasury() - $position->getCost();

                $team->setTreasury($tresors);

                $tv = $team->getTv() + $position->getCost();

                $team->setTv($tv);

                $pcost = $position->getCost();

                $allskills = $this->getDoctrine()->getRepository(GameDataSkills::class)->findAll();

                $playerskills = explode(",", $position->getSkills());

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

                $entityManager = $this->getDoctrine()->getManager();

                $allPlayersTeam = $this->getDoctrine()->getRepository(Players::class)->findBy(
                    ['ownedByTeam' => $team],
                    ['nr' => 'ASC']
                );

                $nr = 1;

                foreach ($allPlayersTeam as $playerteam) {
                    if ($nr == $playerteam->getNr()) {
                        $nr++;
                    } else {
                        break;
                    }

                }

                $player->setNr($nr);
                $player->setDateBought(\DateTime::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s")));
                $player->setFRid($position->getFRace());
                $player->setFPos($position);
                $player->setOwnedByTeam($team);
                $player->setFCid($team->getOwnedByCoach());
                $player->setValue($position->getCost());

                $entityManager->persist($player);

                $entityManager->persist($team);

                $entityManager->flush();

                $html = $this->render(
                    'statbb/lineteamsheet.html.twig',
                    ['position' => $position, 'player' => $player, 'skill' => $listskill]
                )->getContent();
                $reponse = "ok";
            } else {

                $reponse = "pl";
                $html = "Plus de place !";

            }
        } else {

            $reponse = "ar";
            $html = "Pas assez d'argent !";

        }


        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        $serializer = new Serializer($normalizers, $encoders);

        $response = array(
            "html" => $html,
            "tv" => $tv,
            "ptv" => ($tv / 1000),
            "tresor" => $tresors,
            "playercost" => $pcost,
            "reponse" => $reponse,
        );

        $jsonContent = $serializer->serialize($response, 'json');

        return new JsonResponse($jsonContent);

    }

    /**
     * @Route("/remPlayer/{playerId}", options = { "expose" = true })
     */
    public function remPlayer($playerId)
    {
        $effect = "sld";

        $entityManager = $this->getDoctrine()->getManager();

        $player = $this->getDoctrine()->getRepository(Players::class)->findOneBy(['playerId' => $playerId]);

        $matchest1 = $this->getDoctrine()->getRepository(Matches::class)->findBy(
            ['team1' => $player->getOwnedByTeam()->getTeamId()]
        );

        $matchest2 = $this->getDoctrine()->getRepository(Matches::class)->findBy(
            ['team2' => $player->getOwnedByTeam()->getTeamId()]
        );

        if (count($matchest1) == 0 || count($matchest2) == 0) {

            $player->getOwnedByTeam()->setTreasury(
                $player->getOwnedByTeam()->getTreasury() + $player->getFPos()->getCost()
            );

            $effect = "rm";
        }

        $tcost = 0;

        $supcomp = $this->getDoctrine()->getRepository(PlayersSkills::class)->findBy(
            ['fPid' => $player->getPlayerId()]
        );

        $listskill = '';

        foreach ($supcomp as $comps) {

            if ($comps->getType() == 'N') {

                $tcost += 20000;
                $listskill .= '<text class="text-success">'.$comps->getFSkill()->getName().'</text>, ';

            } else {

                $tcost += 30000;
                $listskill .= '<text class="text-danger">'.$comps->getFSkill()->getName().'</text>, ';

            }

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

        $tcost = $player->getFPos()->getCost() + $tcost;

        $tv = $player->getOwnedByTeam()->getTv() - $tcost;

        $player->getOwnedByTeam()->setTv($tv);

        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        $serializer = new Serializer($normalizers, $encoders);

        $response = array(
            "tv" => $tv,
            "ptv" => ($tv / 1000),
            "tresor" => $player->getOwnedByTeam()->getTreasury(),
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

    /**
     * @Route("/add_inducement/{teamId}/{type}", options = { "expose" = true })
     */
    public function add_inducement($teamId, $type)
    {

        $team = $this->getDoctrine()->getRepository(Teams::class)->findOneBy(['teamId' => $teamId]);

        $entityManager = $this->getDoctrine()->getManager();

        switch ($type) {

            case "rr":

                $inducost = $team->getFRace()->getCostRr();

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

                    $tv = $team->getTv() + $team->getFRace()->getCostRr();

                    $team->setTv($tv);

                    $inducost = $team->getFRace()->getCostRr();

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

    /**
     * @Route("/rem_inducement/{teamId}/{type}", options = { "expose" = true })
     */
    public function rem_inducement($teamId, $type)
    {

        $team = $this->getDoctrine()->getRepository(Teams::class)->findOneBy(['teamId' => $teamId]);

        $entityManager = $this->getDoctrine()->getManager();

        $matchest1 = $this->getDoctrine()->getRepository(Matches::class)->findBy(['team1' => $team->getTeamId()]);

        $matchest2 = $this->getDoctrine()->getRepository(Matches::class)->findBy(['team2' => $team->getTeamId()]);

        switch ($type) {

            case "rr":

                if ($team->getRerolls() > 0) {
                    $inducost = $team->getFRace()->getCostRr();

                    $nbr = $team->getRerolls() - 1;

                    $team->setRerolls($nbr);

                    if (count($matchest1) == 0 || count($matchest2) == 0) {

                        $treasury = $team->getTreasury() + $inducost;

                        $team->setTreasury($treasury);

                    }

                    $tv = $team->getTv() - $team->getFRace()->getCostRr();

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

    /**
     * @Route("/ret_team/{teamId}", options = { "expose" = true })
     */
    public function ret_team($teamId)
    {
        $team = $this->getDoctrine()->getRepository(Teams::class)->findOneBy(['teamId' => $teamId]);

        $entityManager = $this->getDoctrine()->getManager();

        $team->setRetired(1);

        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        $serializer = new Serializer($normalizers, $encoders);

        $response = array();

        $entityManager->persist($team);

        $entityManager->flush();

        $jsonContent = $serializer->serialize($response, 'json');

        return new JsonResponse($jsonContent);

    }

    /**
     * @Route("/dropdownTeams/{nbr}")
     */

    public function dropdownTeams($nbr)
    {
        $setting = $this->getDoctrine()->getRepository(Setting::class)->findOneBy(['name' => 'year']);

        $teams = $this->getDoctrine()->getRepository(Teams::class)->findBy(
            ['retired' => 0, 'year' => $setting->getValue()],
            ['name' => 'ASC']
        );

        return $this->render('statbb/dropdownteams.html.twig', ['teams' => $teams, 'nbr' => $nbr]);


    }

    /**
     * @Route("/dropdownPlayer/{teamId}/{nbr}", options = { "expose" = true })
     */

    public function dropdownPlayer($teamId, $nbr)
    {

        $entityManager = $this->getDoctrine()->getManager();

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

        $match->setDateCreated(\DateTime::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s")));
        $match->setFans($parametersAsArray['totalpop']);
        $match->setFfactor1($parametersAsArray['varpop_team1']);
        $match->setFfactor2($parametersAsArray['varpop_team2']);
        $match->setIncome1($parametersAsArray['gain1']);
        $match->setIncome2($parametersAsArray['gain2']);
        $match->setTeam1Score($parametersAsArray['score1']);
        $match->setTeam2Score($parametersAsArray['score2']);
        $match->setTeam1($team1);
        $match->setTeam2($team2);
        $match->setTv1($team1->getTv());
        $match->setTv2($team2->getTv());

        $entityManager->persist($match);

        $players = $this->getDoctrine()->getRepository(Players::class)->findBy(['ownedByTeam' => $team1->getTeamId()]);

        $tt = 0;

        foreach ($players as $player) {
            if ($player->getStatus() == 1 || $player->getStatus() == 9 || $player->getInjRpm() == 0) {
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

        $players = $this->getDoctrine()->getRepository(Players::class)->findBy(['ownedByTeam' => $team2->getTeamId()]);

        foreach ($players as $player) {
            if ($player->getStatus() == 1 || $player->getStatus() == 9 || $player->getInjRpm() == 0) {

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

        $tt = $this->getDoctrine()->getRepository(MatchData::class)->findOneBy(
            ['fPlayer' => $parametersAsArray['player']['0']['id'], 'fMatch' => $match->getMatchId()]
        );


        foreach ($parametersAsArray['player'] as $action) {

            $mdplayer = $this->getDoctrine()->getRepository(MatchData::class)->findOneBy(
                ['fPlayer' => $action['id'], 'fMatch' => $match->getMatchId()]
            );
            $playerstat = $this->getDoctrine()->getRepository(Players::class)->findOneBy(['playerId' => $action['id']]);

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

                    $playerstat->setDateDied(\DateTime::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s")));
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
                case 0 :
                    if ($spp > 5) {

                        $playerstat->setStatus(9);

                    }
                    break;

                case 1 :
                    if ($spp > 15) {

                        $playerstat->setStatus(9);

                    }
                    break;

                case 2 :
                    if ($spp > 30) {

                        $playerstat->setStatus(9);

                    }
                    break;

                case 3 :
                    if ($spp > 50) {

                        $playerstat->setStatus(9);

                    }
                    break;

                case 4 :
                    if ($spp > 75) {

                        $playerstat->setStatus(9);

                    }
                    break;

                case 5 :
                    if ($spp > 175) {

                        $playerstat->setStatus(9);

                    }
                    break;

            }

            $entityManager->persist($playerstat);

        }


        $team1->setTv($this->calculateTV($team1) * 1000);
        $team2->setTv($this->calculateTV($team2) * 1000);

        $team1->setRdy(0);
        $team2->setRdy(0);


        $entityManager->persist($team1);
        $entityManager->persist($team2);

        $entityManager->flush();

        $tt = 'ok';

        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        $serializer = new Serializer($normalizers, $encoders);

        $jsonContent = $serializer->serialize($tt, 'json');

        return new JsonResponse($jsonContent);

    }

    public function calculateTV($team)
    {
        $tv = 0;

        $tcost = 0;

        $team = $this->getDoctrine()->getRepository(Teams::class)->find($team->getTeamId());

        $players = $this->getDoctrine()->getRepository(Players::class)->findBy(
            array('ownedByTeam' => $team->getTeamId()),
            array('nr' => 'ASC')
        );

        foreach ($players as $player) {

            if ($player->getStatus() != 7 && $player->getStatus() != 8) {

                $pcost = 0;

                $pcost += $player->getFPos()->getCost();

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


        $tcost += $team->getRerolls() * $team->getFRace()->getCostRr();
        $tcost += ($team->getFf() + $team->getFfBought()) * 10000;
        $tcost += $team->getAssCoaches() * 10000;
        $tcost += $team->getCheerleaders() * 10000;
        $tcost += $team->getApothecary() * 50000;


        $tv = $tcost / 1000;

        return $tv;
    }

    /**
     * @Route("/chkteam/{teamId}", name="Chkteam", options = { "expose" = true })
     */
    public function chkteam($teamId)
    {

        $entityManager = $this->getDoctrine()->getManager();

        $team = $this->getDoctrine()->getRepository(Teams::class)->findOneBy(['teamId' => $teamId]);
        $players = $this->getDoctrine()->getRepository(Players::class)->findBy(['ownedByTeam' => $team->getTeamId()]);

        if ($team->getFRace()->getRaceId() == 17) {

            $positionJr = $this->getDoctrine()->getRepository(GameDataPlayers::class)->findOneBy(['posId' => '171']);

        } else {

            $positionJr = $this->getDoctrine()->getRepository(GameDataPlayers::class)->findOneBy(
                ['fRace' => $team->getFRace(), 'qty' => '16']
            );

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

            $nbrskill += count($skills) + $player->getAchAg() + $player->getAchAv() + $player->getAchMa(
                ) + $player->getAchSt();

            switch ($nbrskill) {
                case 0 :
                    if ($spp > 5) {

                        $player->setStatus(9);

                    }
                    break;

                case 1 :
                    if ($spp > 15) {

                        $player->setStatus(9);

                    }
                    break;

                case 2 :
                    if ($spp > 30) {

                        $player->setStatus(9);

                    }
                    break;

                case 3 :
                    if ($spp > 50) {

                        $player->setStatus(9);

                    }
                    break;

                case 4 :
                    if ($spp > 75) {

                        $player->setStatus(9);

                    }
                    break;

                case 5 :
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
                $jr->setDateBought(\DateTime::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s")));
                $jr->setFRid($positionJr->getFRace());
                $jr->setFPos($positionJr);
                $jr->setOwnedByTeam($team);
                $jr->setFCid($team->getOwnedByCoach());
                $jr->setStatus(1);
                $jr->setValue($positionJr->getCost());

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
                $player->setDateSold(\DateTime::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s")));


                $entityManager->persist($player);
                $entityManager->flush();

                $number--;
                if ($number < 11) {
                    break;
                }
            }

        }

        $team->setTv($this->calculateTV($team) * 1000);
        $team->setRdy(1);

        $entityManager->persist($team);
        $entityManager->flush();

        return $this->redirectToRoute('team', ['id' => $team->getTeamId(), 'type' => 'n']);

    }

    /**
     * @Route("/skillmodal/{playerid}", name="skillmodal", options = { "expose" = true })
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
     */
    public function addComp($skillid, $playerid)
    {

        $entityManager = $this->getDoctrine()->getManager();

        $skill = $this->getDoctrine()->getRepository(GameDataSkills::class)->findOneBy(['skillId' => $skillid]);

        $player = $this->getDoctrine()->getRepository(players::class)->findOneBy(['playerId' => $playerid]);

        $toadd = new PlayersSkills();

        $toadd->setFPid($player);

        $toadd->setFSkill($skill);

        $norm = $player->getFPos()->getNorm();

        $pos = stripos($norm, $skill->getCat());

        if ($pos === false) {

        } else {

            $toadd->setType('N');

        }

        $double = $player->getFPos()->getDoub();

        $pos = stripos($double, $skill->getCat());

        if ($pos === false) {

        } else {

            $toadd->setType('D');

        }


        $entityManager->persist($toadd);
        $entityManager->flush();

        $player->setStatus(1);

        $entityManager->persist($player);
        $entityManager->flush();

        $response = new Response();
        $response->setContent($player->getOwnedByTeam()->getTeamId());
        $response->setStatusCode(200);

        return $response;
    }

    /**
     * @Route("/changeNr/{newnr}/{playerid}", name="changeNr", options = { "expose" = true })
     */
    public function changeNr($newnr, $playerid)
    {

        $entityManager = $this->getDoctrine()->getManager();

        $player = $this->getDoctrine()->getRepository(players::class)->findOneBy(['playerId' => $playerid]);

        $player->setNr($newnr);

        $entityManager->persist($player);
        $entityManager->flush();

        $response = new Response();
        $response->setContent($player->getOwnedByTeam()->getTeamId());
        $response->setStatusCode(200);

        return $response;
    }

    /**
     * @Route("/changeName/{newname}/{playerid}", name="changeName", options = { "expose" = true })
     */
    public function changeName($newname, $playerid)
    {

        $entityManager = $this->getDoctrine()->getManager();

        $player = $this->getDoctrine()->getRepository(players::class)->findOneBy(['playerId' => $playerid]);

        $player->setName($newname);

        $entityManager->persist($player);
        $entityManager->flush();

        $response = new Response();
        $response->setContent($player->getOwnedByTeam()->getTeamId());
        $response->setStatusCode(200);

        return $response;
    }

}
