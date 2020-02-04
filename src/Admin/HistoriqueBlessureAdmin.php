<?php

declare(strict_types=1);

namespace App\Admin;

use App\Enum\BlessuresEnum;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

final class HistoriqueBlessureAdmin extends AbstractAdmin
{

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('blessure')
            ->add('date')
            ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $blessureLabel = (new BlessuresEnum)->numeroToBlessure();

        $listMapper
            ->add('blessure', 'choice', ['editable' => true, 'choices' => $blessureLabel])
            ->add('Player.name', null, ['label' => 'Joueur'])
            ->add('Player.ownedByTeam.name', null, ['label' => 'Equipe'])
            ->add('date', 'date', ['editable' => true, 'format' => 'd-m-Y'] );
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->add('blessure')
            ->add('date')
            ;
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('id')
            ->add('blessure')
            ->add('date')
            ;
    }
}
