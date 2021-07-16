<?php

namespace App\Form;

use App\Enum\RulesetEnum;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreerEquipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add("Name", TextType::class, ['label'=>"Nom de l'équipe", 'required' => 'true'])
            ->add(
                RulesetEnum::getChampRaceFromIntByRuleset($options['ruleset']),
                EntityType::class,
                [
                    'class'=> RulesetEnum::getRaceRepoFromIntByRuleset($options['ruleset']),
                    'query_builder' => function (EntityRepository $entityRepository) {
                        return $entityRepository
                            ->createQueryBuilder('Race')
                            ->orderBy('Race.name', 'ASC');
                    },
                    'choice_label' =>'name',
                    'label'=>'Choisir une Race'
                ]
            )
            ->add('submit', SubmitType::class, ['label' => 'Créer'])
            ->add('cancel', ButtonType::class, ['label'=>'Annuler','attr'=>['data-dismiss'=>'modal']])
            ->getForm();
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'equipe' => 0,
                'ruleset' => 0
            ]
        );
    }
}
