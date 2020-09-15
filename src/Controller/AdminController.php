<?php

namespace App\Controller;

use App\Service\SettingsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(SettingsService $settingsService)
    {
        return $this->render('statbb/admin.html.twig', [
            'annee' => $settingsService->anneeCourante(),
        ]);
    }
}
