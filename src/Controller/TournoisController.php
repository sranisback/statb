<?php

namespace App\Controller;

use App\Entity\GameDataPlayers;
use App\Entity\Races;
use App\Service\PlayerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class TournoisController extends AbstractController
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
     * @Route("/tournois", name="tournois")
     */
    public function index()
    {
        return $this->render('statbb/feuilleTournois.html.twig',[
            'listeRaces' => $this->getDoctrine()->getRepository(Races::class)->findBy([],['name' => 'ASC'])
        ]);
    }

    /**
     * @Route("/listePosition/{raceId}", name="listePosition", options = { "expose" = true } )
     */
    public function listePosition($raceId)
    {
        $listePosition = $this->getDoctrine()->getRepository(GameDataPlayers::class)->findBy(
            ['fRace' => $this->getDoctrine()->getRepository(Races::class)->findOneBy(['raceId' => $raceId])]
        );

        return self::transformeEnJson($listePosition);
    }

    /**
     * @Route("/nombreVersComp/{positionId}", name="nombreVersComp", options = { "expose" = true } )
     * @param PlayerService $playerService
     * @param $positionId
     * @return Response
     */
    public function nombreVersComp(PlayerService $playerService, $positionId)
    {
        $listeCompHtml =
            $playerService->listeDesCompdUnePosition(
                $this->getDoctrine()->getRepository(GameDataPlayers::class)->findOneBy(
                    ['posId' => $positionId ]
                )
            );

        return new Response($listeCompHtml);
    }
}
