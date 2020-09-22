<?php

namespace App\Controller;

use App\Service\SettingsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends AbstractController
{
    /**
     * @Route("/Admin", name="Admin")
     */
    public function index(SettingsService $settingsService) : Response
    {
        return $this->render('statbb/admin.html.twig', [
            'annee' => $settingsService->anneeCourante(),
        ]);
    }
}
