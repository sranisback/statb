<?php

namespace App\Admin;

use App\Entity\GameDataStadium;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class StadesAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('nom', null, ['label' => 'Nom'])
            ->add('total_payement', TextType::class, ['label' => 'Payement total'])
            ->add(
                'fTypeStade',
                entityType::class,
                [
                    'class' => GameDataStadium::class,
                    'choice_label' => 'type',
                    'label' => 'Type',
                    'group_by' => 'famille',
                ]
            )
            ->add('niveau', IntegerType::class , ['label' => 'Niveau']);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('nom', null, ['label' => 'Nom']);
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id', null, ['label' => 'id'])
            ->add('nom')
            ->add('total_payement', null, ['label' => 'Payement total'])
            ->add('fTypeStade.type', null, ['label' => 'Type'])
            ->add('niveau', null, ['editable' => true]);
    }
}
