<?php


namespace App\Service;


use App\Entity\Citations;
use App\Entity\Coaches;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UtilisateurService
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $doctrineEntityManager;

    private CitationService $citationService;

    public function __construct(EntityManagerInterface $doctrineEntityManager, CitationService $citationService)
    {
        $this->doctrineEntityManager = $doctrineEntityManager;
        $this->citationService = $citationService;
    }

    public function creeCoach(Coaches $coach, $form, UserPasswordEncoderInterface $encoder)
    {
        if ($form->isSubmitted() && $form->isValid()) {
            $coach->setRoles(['role' => 'ROLE_USER']);

            $encoded = $encoder->encodePassword($coach, $coach->getPassword());
            $coach->setPassword($encoded);

            $this->doctrineEntityManager->persist($coach);
            $this->doctrineEntityManager->flush();

            return true;
        }

        return false;
    }

    public function ajoutCitation(Citations $citations, $form)
    {
        if ($form->isSubmitted() && $form->isValid()) {
            $this->citationService->enregistrerCitation($citations);

            return true;
        }

        return false;
    }
}