<?php

namespace App\Controller;

use App\Entity\GameDataPlayers;
use App\Entity\GameDataSkills;
use App\Entity\MatchData;
use App\Entity\Matches;
use App\Entity\Players;
use App\Entity\PlayersSkills;
use App\Entity\Teams;
use App\Form\AjoutCompetenceType;
use App\Form\AjoutJoueurType;
use App\Service\EquipeService;
use App\Service\PlayerService;
use App\Tools\randomNameGenerator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class JoueurController extends AbstractController
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
        }

        $listeMatches = $this->getDoctrine()->getRepository(MatchData::class)->listeDesMatchsdUnJoueur($joueur);
        $count = 0;

        /** @var Matches $match */
        foreach ($listeMatches as $match) {
            $msdata[$count]["mId"] = $match->getMatchId();
            if (!empty($joueur)) {
                $actions = $playerService->actionDuJoueurDansUnMatch($match, $joueur);
            }
            if (!empty($actions)) {
                $msdata[$count]["data"] = substr($actions, 0, strlen($actions) - 2);
            } else {
                $msdata[$count]["data"] = '';
            };

            $count++;
        }

        if ($count == 0) {
            $msdata[$count]["mId"] = 0;
            $msdata[$count]["data"] = '';
        }

        if ($type == "modal") {
            return $this->render(
                'statbb/player_modal.html.twig',
                [
                    'player' => $joueur,
                    'pdata' => $pdata,
                    'matches' => $listeMatches,
                    'mdata' => $msdata,
                ]
            );
        } else {
            return $this->render(
                'statbb/player.html.twig',
                [
                    'player' => $joueur,
                    'pdata' => $pdata,
                    'matches' => $listeMatches,
                    'mdata' => $msdata,
                ]
            );
        }
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
     * @Route("/playerAdder/{equipe}", name="playerAdder")
     * @param Teams $equipe
     * @return Response
     */
    public function playerAdder(Teams $equipe)
    {
        $form = $this->createForm(AjoutJoueurType::class, null, ['equipe' => $equipe]);

        return $this->render('statbb/playeradder.html.twig', ['form' => $form->createView(), 'equipe' => $equipe]);
    }

    /**
     * @Route("/addPlayer", name="addPlayer",options = { "expose" = true })
     * @param PlayerService $playerService
     * @param EquipeService $equipeService
     * @param Request $request
     * @return JsonResponse
     */
    public function addPlayer(PlayerService $playerService, EquipeService $equipeService, Request $request)
    {
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
            if ($position) {
                $competences = $playerService->listeDesCompdDeBasedUnJoueur($joueur);

                $competences = substr($competences, 0, strlen($competences) - 2);

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

        if (!empty($equipe)) {
            $equipe->setTv($tv);

            $this->getDoctrine()->getManager()->persist($equipe);

            $this->getDoctrine()->getManager()->flush();
        }

        $response = [
            "html" => $html,
            "tv" => $tv,
            "ptv" => ($tv / 1000),
            "tresor" => $tresors,
            "playercost" => $coutjoueur,
            "reponse" => $reponse,
/*            "NomJoueur" => $joueur->getName(),
            "NrJoueur" => $joueur->getNr(),
            "PositionJoueur" => $joueur->getFPos()->getPos()*/
        ];

        return self::transformeEnJson($response);
    }

    /**
     * @Route("/remPlayer/{playerId}", options = { "expose" = true })
     * @param PlayerService $playerService
     * @param int $playerId
     * @return JsonResponse
     */
    public function remPlayer(PlayerService $playerService, $playerId)
    {
        $resultat[''] = '';
        $joueur = $this->getDoctrine()->getRepository(Players::class)->findOneBy(['playerId' => $playerId]);

        if ($joueur) {
            $resultat = $playerService->renvoisOuSuppressionJoueur($joueur);
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
     * @return string
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

        return 'erreur';
    }

    /**
     * @Route("/genereNom", name="genereNom", options = { "expose" = true })
     * @return mixed
     */
    public function genereNomJoueur()
    {
        $generateurDeNom = new randomNameGenerator();
        $nom = $generateurDeNom->generateNames(1);

        return new Response($nom[0]);
    }

    /**
     * @Route("/genereNumero", name="genereNumero", options = { "expose" = true })
     * @param PlayerService $playerService
     * @param Request $request
     * @return mixed
     */
    public function genereNumero(PlayerService $playerService, Request $request)
    {
        $donnees = $request->request->all();
        $equipe = $this->getDoctrine()->getRepository(Teams::class)->findOneBy(['teamId' => $donnees['equipeId']]);
        if (!empty($equipe)) {
            return new Response($playerService->numeroLibreDelEquipe($equipe));
        }

        return new Response(99);
    }
}
