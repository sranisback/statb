<?php

namespace App\Controller;

use App\Entity\Infos;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InfosController extends AbstractController
{

    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @Route("/infos", name="infos")
     */
    public function infos(): Response
    {
        return $this->render('statbb/infos.html.twig', [
            'news' => $this->doctrine->getRepository(Infos::class)->cinqDernieresNews(),
        ]);
    }

    /**
     * @Route ("/infosCompletes", name="infosCompletes")
     */
    public function infosCompletes(): Response
    {
        return $this->render('statbb/infosCompletes.html.twig', [
            'news' => $this->doctrine->getRepository(Infos::class)->findAll()
        ]);
    }
}
