<?php

namespace App\Controller;

use App\Entity\Primes;
use App\Form\PrimeType;
use App\Form\RealiserPrimeType;
use App\Service\PrimeService;
use App\Service\SettingsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PrimeController extends AbstractController
{
    /**
     * @Route("/ajoutPrimeForm/{coachId}/{primeId}", name="ajoutPrimeForm", options = { "expose" = true })
     * @param int $coachId
     * @param null $primeId
     * @return Response
     */
    public function ajoutPrimeForm($coachId, $primeId = null)
    {
        $prime = new Primes();

        if ($primeId == null) {
            $prime = $this->getDoctrine()->getRepository(Primes::class)->findOneBy(['id' => $primeId]);
        }

        $form = $this->createForm(PrimeType::class, $prime, ['coach' => $coachId]);

        return $this->render('statbb/ajoutPrime.html.twig', ['form' => $form->createView(), 'coachId' => $coachId]);
    }

    /**
     * @Route("/ajoutPrime/{coachId}", name="ajoutPrime", options = { "expose" = true })
     * @param Request $request
     * @param PrimeService $primeService
     * @param int $coachId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function ajoutPrime(Request $request, PrimeService $primeService, $coachId)
    {
        if ($primeService->creationPrime($coachId, $request->request->get('prime'))) {
            $this->addFlash('success', 'Prime Ajoutée');
        }

        return $this->redirectToRoute('frontUser');
    }

    /**
     * @Route("/montrePrimesEnCours", name="montrePrimesEnCours", options = { "expose" = true })
     * @param SettingsService $settingsService
     * @return Response
     */
    public function montrePrimesEnCours(SettingsService $settingsService)
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
     * @Route("/supprimerPrime/{primeId}", name="supprimerPrime", options = { "expose" = true })
     * @param PrimeService $primeService
     * @param int $primeId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function supprimerPrime(PrimeService $primeService, $primeId)
    {
        if ($primeService->supprimerPrime($primeId)) {
            $this->addFlash('success', 'Prime Supprimée');
        }

        return $this->redirectToRoute('frontUser');
    }

    /**
     * @Route("/realiserPrimeForm", name="realiserPrimeForm", options = { "expose" = true })
     * @param SettingsService $settingsService
     * @return Response
     */
    public function realiserPrimeForm(SettingsService $settingsService)
    {
        $prime = new Primes();

        $form = $this->createForm(RealiserPrimeType::class, $prime);

        return $this->render('statbb/realisationPrime.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/realiserPrime", name="realiserPrime", options = { "expose" = true })
     * @param Request $request
     * @param PrimeService $primeService
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function realiserPrime(Request $request, PrimeService $primeService)
    {
        if ($primeService->realiserPrime($request->request->get('realiser_prime'))) {
            $this->addFlash('success', 'Prime Réalisée');
        }

        return $this->redirectToRoute('frontUser');
    }
}
