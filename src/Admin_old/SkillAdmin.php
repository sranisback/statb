<?php


namespace App\Admin_old;

use App\Entity\GameDataSkills;
use App\Entity\Players;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class SkillAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->add('fPid', EntityType::class, ['class' => Players::class, 'choice_label' => 'name', 'label' => 'joueur'])
            ->add(
                'fSkill',
                EntityType::class,
                ['class' => GameDataSkills::class, 'choice_label' => 'name', 'label' => 'Compétence']
            )
            ->add('type', ChoiceType::class, ['choices' => ['N' => 'N', 'D' => 'D']]);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('fPid.name', null, ['label' => 'Joueur'])
            ->add('fSkill.name', null, ['label' => 'Compétence'])
            ->add('fPid.ownedByTeam.year', null, ['label' => 'Année'])
            ->add('fPid.ownedByTeam.name', null, ['label' => 'Equipe'])
            ->add('type');
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('fPid.nr', null, ['label' => 'Numero'])
            ->addIdentifier('fPid.name', null, ['label' => 'Joueur'])
            ->add('fPid.ownedByTeam.name', null, ['label' => 'Equipe'])
            ->add('fSkill.name', null, ['label' => 'Compétence'])
            ->add('type')
            ->add(
                '_action',
                null,
                [
                    'actions' => [
                        'edit' => [],
                        'delete' => [],
                    ],
                ]
            );
    }
}
