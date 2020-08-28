<?php

namespace App\Admin_old;

use App\Entity\Coaches;
use App\Entity\Players;
use App\Entity\Teams;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class PrimeAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->add('montant')
            ->add(
                'players',
                entityType::class,
                [
                    'class' => Players::class,
                    'choice_label' => 'name',
                    'label' => 'Victime',
                    'group_by' => 'ownedByTeam.name',
                ]
            )
            ->add(
                'coaches',
                entityType::class,
                ['class' => Coaches::class, 'choice_label' => 'name', 'label' => 'Coache source']
            )
            ->add(
                'teams',
                entityType::class,
                [
                    'class' => Teams::class,
                    'choice_label' => 'name',
                    'label' => 'Equipe Source',
                    'group_by' => 'ownedByCoach.name',
                ]
            )
            ->add('actif', null, ['label' => 'Active']);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('teams.name', null, ['label' => 'De (Equipe Source)'])
            ->add('players.name', null, ['label' => 'Victime'])
            ->add('players.ownedByTeam.name', null, ['label' => 'Equipe victime'])
            ->add('actif', null, ['label' => 'Active']);
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('id', null, ['label' => 'id'])
            ->add('montant')
            ->add('teams.name', null, ['label' => 'De (Equipe Source)'])
            ->add('players.name', null, ['label' => 'Victime'])
            ->add('players.ownedByTeam.name', null, ['label' => 'Equipe victime'])
            ->add('actif', null, ['label' => 'Active'])
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
        ;
    }
}
