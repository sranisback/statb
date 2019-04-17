<?php

namespace App\Form;


use App\Entity\Primes;
use App\Entity\Teams;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RealiserPrimeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'Primes',
                EntityType::class,
                ['class'=>Primes::class,
                    'query_builder' =>function(EntityRepository $entityRepository) {
                        return $entityRepository->createQueryBuilder('Primes')
                            ->where('Primes.actif = 1');
                    },
                    'choice_label' =>function(Primes $prime){
                        return $prime->getMontant().'PO  de '.$prime->getPlayers()->getOwnedByTeam()->getName();
                    },
                    'group_by' =>function(Primes $prime){
                        $nomDuJoueur = 'Inconnu';

                        if (strlen($prime->getPlayers()->getName()) != 2) {
                            $nomDuJoueur = $prime->getPlayers()->getName();
                        }
                        return $prime->getPlayers()->getNr().'. '.$nomDuJoueur.', '.$prime->getPlayers()->getFPos()->getPos().' de '.$prime->getPlayers()->getOwnedByTeam()->getName();
                    },
                    'label'=>'Choisir une Prime','mapped'=>false
                ]
            )
            ->add('Teams',
                EntityType::class,
                [
                    'class'=>Teams::class,
                    'choice_label'=>'name',
                    'query_builder'=>function(EntityRepository $entityRepository) use($options) {
                        return $entityRepository->createQueryBuilder('Teams')
                            ->where('Teams.year ='.$options['year']);
                    },
                    'group_by' =>function (Teams $equipe) {
                        return $equipe->getOwnedByCoach()->getName();
                    }
                ]
            )
            ->add('submit', SubmitType::class, ['label' => 'Realiser'])
            ->add('cancel', ButtonType::class, ['label'=>'Annuler','attr'=>['data-dismiss'=>'modal']])
            ->getForm();;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
           'year' => 0
        ]);
    }
}