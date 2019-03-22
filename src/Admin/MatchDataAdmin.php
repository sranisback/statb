<?php

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class MatchDataAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('mvp', null)
            ->add('cp', null)
            ->add('td', null)
            ->add('intcpt', null)
            ->add('bh', null)
            ->add('si', null)
            ->add('ki', null)
            ->add('inj', null)
            ->add('agg', null);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('fMatch.matchId', null, ['label' => 'Match'])
            ->add('fPlayer.name', null, ['label' => 'Joueur'])
            ->add('fPlayer.ownedByTeam.name', null, ['label' => 'Equipe'])
            ->add('fPlayer.ownedByTeam.year', null, ['label' => 'Année']);
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('fMatch.matchId', null, ['label' => 'Match'])
            ->add('fPlayer.name', null, ['label' => 'Joueur'])
            ->add('fPlayer.ownedByTeam.name', null, ['label' => 'Equipe'])
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
