<?php

namespace App\Controller;

use App\Entity\Players;
use App\Entity\Teams;
use App\Service\EquipeService;
use App\Service\PlayerService;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ExportController extends AbstractController
{
    /**
     * @Route("/pdfTeam/{id}", name="pdfTeam")
     * @param PlayerService $playerService
     * @param EquipeService $equipeService
     * @param int $id
     */
    public function pdfTeam(PlayerService $playerService, EquipeService $equipeService, int $id): void
    {
        /** @var Teams $equipe */
        $equipe = $this->getDoctrine()->getRepository(Teams::class)->find($id);

        $count = 0;

        $pdata = [];
        $pdata[] = [];

        $joueurCollection = $this->getDoctrine()->getRepository(Players::class)->listeDesJoueursActifsPourlEquipe(
            $equipe
        );

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
            $pdata[$count]['exp'] = $actionJoueur['exp'];
            $pdata[$count]['bonusXP'] = $actionJoueur['bonus'];
            $pdata[$count]['skill'] = substr($listeCompetence, 0, strlen($listeCompetence) - 2);
            $pdata[$count]['spp'] = $playerService->xpDuJoueur($joueur);
            if ($joueur->getInjRpm() != 0) {
                $pdata[$count]['cost'] = '<s>' . $playerService->valeurDunJoueur($joueur) . '</s>';
            } else {
                $pdata[$count]['cost'] = $playerService->valeurDunJoueur($joueur);
            }
            $pdata[$count]['status'] = $playerService->statutDuJoueur($joueur);

            $count++;
        }

        $tdata = $equipeService->valeurInducementDelEquipe($equipe);
        $tdata['playersCost'] = $playerService->coutTotalJoueurs($equipe);
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

        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->setIsRemoteEnabled(true);
        $pdfOptions->setIsHtml5ParserEnabled(true);

        $dompdf = new Dompdf($pdfOptions);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $dompdf->stream($equipe->getName() . '.pdf', ["Attachment" => true]);
    }

    /**
     * @Route("/pdfTournois", name="pdfTournois")
     * @param Request $request
     * @return Response
     */
    public function pdfTournois(Request $request)
    {
        $ignore = ['.', '..'];
        $nbr = -2;
        foreach (scandir($this->getParameter('pdf_directory')) as $fichier) {
            if (!in_array($fichier, $ignore)
                &&
                filemtime(
                    $this->getParameter('pdf_directory') . DIRECTORY_SEPARATOR . $fichier
                ) < strtotime('- 2 weeks')) {
                unlink($this->getParameter('pdf_directory') . DIRECTORY_SEPARATOR . $fichier);
            }

            if ($nbr > 7) {
                unlink($this->getParameter('pdf_directory') . DIRECTORY_SEPARATOR . $fichier);
            }
            $nbr++;
        }

        $request = $request->request->all();

        $json = json_decode($request['post']);

        $json[1] = str_replace('<th class="first"></th>', '', $json[1]);
        $json[1] = str_replace(
            '<td class="first">
                <span onclick="supprimerJoueur(this)" class="fas fa-times text-danger" aria-hidden="true"></span>
                </td>',
            '',
            $json[1]
        );

        $html = $this->renderView(
            'statbb/pdfTournois.html.twig',
            [
                'nomEquipe' => $json[0],
                'feuilleEquipe' => $json[1],
                'coachEquipe' => $json[2],
                'raceEquipe' => $json[3],
                'coutTotalJoueur' => $json[4],
                'tresorEquipe' => $json[5],
                'depenseEquipe' => $json[6],
                'totalAutorise' => $json[7],
                'relancesEquipe' => $json[8],
                'totalRelanceEquipe' => $json[9],
                'popEquipe' => $json[10],
                'coutPopEquipe' => $json[11],
                'assistants' => $json[12],
                'totalAssistant' => $json[13],
                'pompom' => $json[14],
                'totalPompom' => $json[15],
                'apoticaire' => $json[16],
                'tvTotal' => $json[17],
                'stadeEquipe' => $json[18],
                'CoutrelancesEquipe' => $json[19],
                'totalapo' => $json[19]
            ]
        );

        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->setIsRemoteEnabled(true);
        $pdfOptions->setIsHtml5ParserEnabled(true);

        $dompdf = new Dompdf($pdfOptions);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $hash = hash('md5', date('Y-m-d h:i:s'));
        $nomFichier = 'Equipe' . $hash . '.pdf';

        file_put_contents($this->getParameter('pdf_directory') . '/' . $nomFichier, $dompdf->output());

        return new Response(
            $this->renderView(
                'statbb/pdfDownload.html.twig',
                ['file' => $nomFichier]
            )
        );
    }
}
