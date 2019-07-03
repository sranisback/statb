<?php

namespace App\Controller;

use App\Entity\GameDataPlayers;
use App\Entity\GameDataStadium;
use App\Entity\MatchData;
use App\Entity\Matches;
use App\Entity\Players;
use App\Entity\PlayersSkills;
use App\Entity\Teams;
use App\Form\CreerStadeType;
use App\Service\EquipeService;
use App\Service\PlayerService;
use App\Service\SettingsService;
use App\Form\CreerEquipeType;

use App\Service\StadeService;
use DateTime;
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
     * @Route("/montreLesEquipes", name="showteams", options = { "expose" = true })
     * @param SettingsService $settingsService
     * @return response
     */
    public function montreLesEquipes(SettingsService $settingsService)
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
     * @param EquipeService $equipeService
     * @param PlayerService $playerService
     * @return response
     */
    public function showUserTeams(
        SettingsService $settingsService,
        EquipeService $equipeService,
        PlayerService $playerService
    ) {
        $tdata = [];

        $equipesCollection = $this->getDoctrine()->getRepository(Teams::class)->findBy(
            ['ownedByCoach' => $this->getUser(), 'year' => $settingsService->anneeCourante()]
        );

        $countEquipe = 0;

        foreach ($equipesCollection as $number => $equipe) {
            $resultats = $equipeService->resultatsDelEquipe(
                $equipe,
                $this->getDoctrine()->getRepository(Matches::class)->listeDesMatchs($equipe)
            );
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
            $players = $this->getDoctrine()->getRepository(Players::class)->listeDesJoueursPourlEquipe($equipe);

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

        if ($teamid != 0) {
            $this->addFlash('success', 'Equipe Ajoutée!');
        }

        return $this->redirectToRoute('team', ['teamid' => $teamid, 'type' => 'n']);
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
     * @Route("/chkteam/{teamId}", name="Chkteam", options = { "expose" = true })
     * @param int $teamId
     * @param EquipeService $equipeService
     * @param PlayerService $playerService
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function chkteam($teamId, EquipeService $equipeService, PlayerService $playerService)
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

            $team->setTv($equipeService->tvDelEquipe($team, $playerService));

            $entityManager->persist($team);
            $entityManager->flush();

            return $this->redirectToRoute('team', ['teamid' => $team->getTeamId(), 'type' => 'n'], 302);
        }

        return $this->redirectToRoute('index');
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
     * @Route("/recalculerTV", name="recalculerTV", options = { "expose" = true })
     * @param EquipeService $equipeService
     * @param PlayerService $playerService
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function recalculerTV(EquipeService $equipeService, PlayerService $playerService)
    {
        $entityManager = $this->getDoctrine()->getManager();

        foreach ($this->getDoctrine()->getRepository(Teams::class)->findAll() as $equipe) {
            $equipe->setTv($equipeService->tvDelEquipe($equipe, $playerService));

            $entityManager->persist($equipe);
            $entityManager->flush();
        }

        return $this->redirectToRoute('frontUser');
    }
}
