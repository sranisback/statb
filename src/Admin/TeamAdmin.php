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
    /**
     * @var string[]
     */
    protected array $datagridValues = ['_sort_by' => 'name'];

    protected function configureFormFields(FormMapper $formMapper): void
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
            ->add('apothecary', ChoiceType::class, ['choices' => ['Oui' => 1, 'Non' => 0], 'label' => 'Apothicaire'])
            ->add('retired', ChoiceType::class, ['choices' => ['Oui' => 1, 'Non' => 0], 'label' => 'Retirée'])
            ->add('year', null, ['label' => 'Année']);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('name', null, ['label' => 'Nom'])
            ->add('ownedByCoach.name', null, ['label' => 'Coach'])
            ->add('fRace.name', null, ['label' => 'Race'])
            ->add('retired', null, ['label' => 'Retiré'])
            ->add('year', null, ['label' => 'Année']);
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('name', null, ['label' => 'Nom'])
            ->add('ownedByCoach.name', null, ['label' => 'Coach'])
            ->add('fRace.name', null, ['label' => 'Race'])
            ->add('treasury', null, ['label' => 'Trésor', 'editable' => true])
            ->add('rerolls', null, ['editable' => true])
            ->add('ffBought', null, ['label' => 'FF achetée', 'editable' => true])
            ->add('ff', null, ['label' => 'FF gagnée', 'editable' => true])
            ->add('assCoaches', null, ['label' => 'Assistants', 'editable' => true])
            ->add('cheerleaders', null, ['label' => 'Pompom girls', 'editable' => true])
            ->add('tv', null, ['editable' => true])
            ->add('apothecary', 'boolean', ['label' => 'Apothicaire', 'editable' => true])
            ->add('retired', 'boolean', ['label' => 'Retiré', 'editable' => true])
            ->add('franchise', 'boolean', ['label' => 'Franchise ?', 'editable' => true]);
    }
}
