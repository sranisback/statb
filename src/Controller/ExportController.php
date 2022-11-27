<?php

namespace App\Controller;

use App\Entity\Teams;
use App\Service\ExportService;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ExportController extends AbstractController
{
    private ExportService $exportService;

    public function __construct(ExportService $exportService) {
        $this->exportService = $exportService;
    }

    /**
     * @Route("/pdfTeam/{teamId}", name="pdfTeam")
     * @param Teams $equipe
     * @throws \Exception
     */
    public function pdfTeam(Teams $equipe): void
    {
        $dataGenerated = $this->exportService->generatePdf($equipe);

        $html = $this->renderView(
            'statbb/pdfteam.html.twig',
            [
                'players' => $dataGenerated['players'],
                'team' => $dataGenerated['team'],
                'pdata' => $dataGenerated['pdata'],
                'tdata' => $dataGenerated['tdata'],
                'compteur' => $dataGenerated['compteur'],
            ]
        );

        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->setIsRemoteEnabled(true);

        $dompdf = new Dompdf($pdfOptions);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $dompdf->stream($dataGenerated['nom'] . '.pdf', ["Attachment" => true]);
    }

    /**
     * @Route("/pdfTournois", name="pdfTournois")
     * @param Request $request
     * @return Response
     */
    public function pdfTournois(Request $request): Response
    {
        $ignore = ['.', '..'];
        $nbr = -2;

        if (!is_dir($this->getParameter('pdf_directory'))) {
            mkdir($this->getParameter('pdf_directory'));
        }

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
        /* @phpstan-ignore-line */

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
