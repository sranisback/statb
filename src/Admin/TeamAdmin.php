<?php

namespace App\Admin;

use App\Entity\Coaches;
use App\Entity\Races;
use App\Entity\Stades;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

final class TeamAdmin extends AbstractAdmin
{
    protected $datagridValues = ['_sort_by' => 'name'];

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name', null, ['label' => 'Nom'])
            ->add(
                'ownedByCoach',
                EntityType::class,
                ['class' => Coaches::class, 'choice_label' => 'name', 'label' => 'Coach']
            )
            ->add(
                'FStades',
                EntityType::class,
                ['class' => Stades::class, 'choice_label' => 'nom', 'label' => 'Stades']
            )
            ->add('fRace', EntityType::class, ['class' => Races::class, 'choice_label' => 'name', 'label' => 'Race'])
            ->add('treasury', null, ['label' => 'Trésor'])
            ->add('rerolls')
            ->add('ffBought', null, ['label' => 'FF achetée'])
            ->add('ff', null, ['label' => 'FF gagnée'])
            ->add('assCoaches', null, ['label' => 'Assistants'])
            ->add('cheerleaders', null, ['label' => 'Pompom girls'])
            ->add('tv')
            ->add('apothecary', ChoiceType::class, ['choices' => ['Oui' => 1, 'Non' => 0], 'label' => 'Race'])
            ->add('retired', ChoiceType::class, ['choices' => ['Oui' => 1, 'Non' => 0], 'label' => 'Retirée'])
            ->add('year', null, ['label' => 'Année']);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name', null, ['label' => 'Nom'])
            ->add('ownedByCoach.name', null, ['label' => 'Coach'])
            ->add('fRace.name', null, ['label' => 'Race'])
            ->add('retired', null, ['label' => 'Retiré'])
            ->add('year', null, ['label' => 'Année']);
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name', null, ['label' => 'Nom'])
            ->add('ownedByCoach.name', null, ['label' => 'Coach'])
            ->add('fRace.name', null, ['label' => 'Race'])
            ->add('treasury', null, ['label' => 'Trésor'])
            ->add('rerolls')
            ->add('ffBought', null, ['label' => 'FF achetée'])
            ->add('ff', null, ['label' => 'FF gagnée'])
            ->add('assCoaches', null, ['label' => 'Assistants'])
            ->add('cheerleaders', null, ['label' => 'Pompom girls'])
            ->add('tv')
            ->add('apothecary', 'boolean', ['label' => 'Apothicaire'])
            ->add('retired', 'boolean', ['label' => 'Retiré'])
            ->add('fStades.nom', null, ['label' => 'Stade']);
    }
}
