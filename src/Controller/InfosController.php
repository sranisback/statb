<?php

namespace App\Controller;

use App\Entity\Infos;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class InfosController extends AbstractController
{
    /**
     * @Route("/infos", name="infos")
     */
    public function infos()
    {
        return $this->render('statbb/infos.html.twig', [
            'news' => $this->getDoctrine()->getRepository(Infos::class)->cinqDernieresNews(),
        ]);
    }
}
