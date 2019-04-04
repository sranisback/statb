<?php

namespace App\form;


use App\Entity\GameDataSkills;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class AjoutCompetenceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'fSkill',
                EntityType::class,
                [
                    'class' => GameDataSkills::class,
                    'choice_label' => 'name',
                    'group_by' => function(GameDataSkills $comp){
                        $listeCategoriesCompetences =
                            [''=>'','G'=>'Générale','A'=>'Agilité','P'=>'Passe','S'=>'Force','M'=>'Mutations','E'=>'Exceptionnelles','C'=>'Statistiques'];
                        return $listeCategoriesCompetences[$comp->getCat()];
                    },
                    'label' => 'Compétence'
                ]
            )
            ->add('submit', SubmitType::class, ['label' => 'Ajouter'])
            ->add('cancel', ButtonType::class, ['label'=>'Annuler','attr'=>['data-dismiss'=>'modal']])
            ->getForm();
    }
}