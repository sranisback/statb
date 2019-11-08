<?php

namespace App\Form;

use App\Entity\GameDataPlayers;
use App\Entity\Teams;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AjoutJoueurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Teams $equipe */
        $equipe = $options['equipe'];
        $race = $equipe->getFRace();

        if (!empty($race)) {
            $builder
                ->add(
                    'fPos',
                    EntityType::class,
                    [
                        'class' => GameDataPlayers::class,
                        'choice_label' => 'pos',
                        'label' => 'Choisir une position',
                        'query_builder' => function (EntityRepository $entityRepository) use ($race) {
                            return $entityRepository->createQueryBuilder('Position')
                                ->where('Position.fRace ='.$race->getRaceId());
                        },
                    ]
                )
                ->add('submit', ButtonType::class, ['label' => 'Ajouter', 'attr' => ['teamId' => $equipe->getTeamId()]])
                ->add('cancel', ButtonType::class, ['label' => 'Annuler', 'attr' => ['data-dismiss' => 'modal']])
                ->getForm();
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'equipe' => 0,
            ]
        );
    }
}
