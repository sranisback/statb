<?php

namespace App\Form;

use App\Enum\RulesetEnum;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AjoutCompetenceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $form = $builder
            ->add(
                RulesetEnum::getChampSkillFromIntByRuleset($options['ruleset']),
                EntityType::class,
                [
                    'class' => RulesetEnum::getGameDataSkillRepoFromIntByRuleset($options['ruleset']),
                    'choice_label' => 'name',
                    'query_builder' =>
                        function (EntityRepository $entityRepository)  {
                            return $entityRepository->createQueryBuilder('Skills')
                                ->where('Skills.cat <> \'E\'')
                                ->orderBy('Skills.name', 'ASC');
                        },
                    'group_by' => function ($comp): string {
                        $listeCategoriesCompetences =
                            [
                                '' => '',
                                'G' => 'Générale',
                                'A' => 'Agilité',
                                'P' => 'Passe',
                                'S' => 'Force',
                                'M' => 'Mutations',
                                'E' => 'Exceptionnelles',
                                'C' => 'Statistiques',
                            ];

                        return $listeCategoriesCompetences[$comp->getCat()];
                    },
                    'label' => 'Compétence',
                ]
            )
            ->add('submit', SubmitType::class, ['label' => 'Ajouter'])
            ->add('cancel', ButtonType::class, ['label' => 'Annuler', 'attr' => ['data-dismiss' => 'modal']]);

            if($options['ruleset'] === RulesetEnum::BB_2020) {
                $form->add('hasard', CheckboxType::class, ['label' => 'Tirée au hasard ?', 'mapped' => false, 'required' => false]);
            }

            $form->getForm();
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'ruleset' => 0
            ]
        );
    }
}
