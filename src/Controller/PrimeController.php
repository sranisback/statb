<?php

namespace App\Controller;

use App\Entity\Primes;
use App\Form\PrimeType;
use App\Form\RealiserPrimeType;
use App\Service\PrimeService;
use App\Service\SettingsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PrimeController extends AbstractController
{
    /**
     * @Route("/ajoutPrimeForm", name="ajoutPrimeForm")
     * @return Response
     */
    public function ajoutPrimeForm(
        Request $request,
        PrimeService $primeService
    ): \Symfony\Component\HttpFoundation\Response {
        $prime = new Primes();
        $form = $this->createForm(PrimeType::class, $prime);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $primeService->creationPrime($request->request->get('prime'));
            $this->addFlash('success', 'Prime Ajoutée');

            return $this->redirectToRoute('frontUser');
        }

        return $this->render('statbb/ajoutPrime.html.twig', ['form' => $form->createView(), 'prime' => $prime]);
    }

    /**
     * @Route("/montrePrimesEnCours", name="montrePrimesEnCours")
     * @param SettingsService $settingsService
     * @return Response
     */
    public function montrePrimesEnCours(SettingsService $settingsService): \Symfony\Component\HttpFoundation\Response
    {
        return $this->render(
            'statbb/tabs/ligue/affichagePrimes.html.twig',
            [
                'primeCollection' => $this->getDoctrine()->getRepository(Primes::class)->listePrimeEnCours(
                    $settingsService->anneeCourante()
                ),
            ]
        );
    }

    /**
     * @Route("/supprimerPrime/{primeId}", name="supprimerPrime")
     * @param PrimeService $primeService
     * @param int $primeId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function supprimerPrime(
        PrimeService $primeService,
        int $primeId
    ): RedirectResponse {
        if ($primeService->supprimerPrime($primeId) !== '') {
            $this->addFlash('success', 'Prime Supprimée');
        }

        return $this->redirectToRoute('frontUser');
    }

    /**
     * @Route("/realiserPrimeForm", name="realiserPrimeForm")
     * @param Request $request
     * @param PrimeService $primeService
     * @return Response
     */
    public function realiserPrimeForm(Request $request, PrimeService $primeService): Response
    {
        $prime = new Primes();
        $form = $this->createForm(RealiserPrimeType::class, $prime);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $primeService->realiserPrime($request->request->get('realiser_prime'));
            $this->addFlash('success', 'Prime Réalisée');

            return $this->redirectToRoute('frontUser');
        }


        return $this->render('statbb/realisationPrime.html.twig', ['prime'=> $prime, 'form' => $form->createView()]);
    }
}
