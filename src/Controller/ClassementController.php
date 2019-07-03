<?php

namespace App\Controller;

use App\Entity\MatchData;
use App\Entity\Matches;
use App\Entity\Players;
use App\Entity\Setting;
use App\Entity\Teams;
use App\Service\SettingsService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ClassementController extends AbstractController
{
    /**
     * @Route("/classement/general/", name="classementgen", options = { "expose" = true })
     * @param SettingsService $settingsService
     * @return Response
     */
    public function classGen(SettingsService $settingsService)
    {
        return $this->render(
            'statbb/classement.html.twig',
            [
                'classement' => $this->getDoctrine()->getRepository(Teams::class)->classement(
                    $settingsService->anneeCourante(),
                    0
                ),
            ]
        );
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
}
