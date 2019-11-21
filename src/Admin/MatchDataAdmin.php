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
            ->add('fMatch.matchId', null, ['label' => 'Match'])
            ->add('fPlayer.nr', null, ['label' => 'Numero Joueur'])
            ->add('fPlayer.name', null, ['label' => 'Joueur'])
            ->add('fPlayer.fPos.pos', null, ['label' => 'Position'])
            ->add('fPlayer.ownedByTeam.name', null, ['label' => 'Equipe'])
            ->add('mvp', null, ['editable' => true])
            ->add('cp', null, ['editable' => true])
            ->add('td', null, ['editable' => true])
            ->add('intcpt', null, ['editable' => true])
            ->add('bh', null, ['editable' => true])
            ->add('si', null, ['editable' => true])
            ->add('ki', null, ['editable' => true])
            ->add('inj', null, ['editable' => true])
            ->add('agg', null, ['editable' => true]);
    }
}
