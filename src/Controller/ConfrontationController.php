<?php

namespace App\Controller;

use App\Service\ConfrontationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConfrontationController extends AbstractController
{

    private ConfrontationService $confrontationService;

    public function __construct(ConfrontationService $confrontationService)
    {
        $this->confrontationService = $confrontationService;
    }

    /**
     * @Route("/confrontation", name="Confrontation")
     */
    public function index(): Response
    {
        return $this->render('confrontation/index.html.twig', [
            'tableExpected' => $this->confrontationService->generateConfrontationTable()
        ]);
    }
}
