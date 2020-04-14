<?php

namespace App\Admin;

use App\Entity\GameDataStadium;
use App\Entity\Meteo;
use App\Entity\Teams;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class MatchAdmin extends AbstractAdmin
{
    /**
     * @var string[]
     */
    protected array $datagridValues = [
        '_sort_order' => 'DESC',
        '_sort_by' => 'dateCreated',
    ];

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->add(
                'team1',
                entityType::class,
                ['class' => Teams::class, 'choice_label' => 'name', 'label' => 'Equipe 1']
            )
            ->add('team1Score', null, ['label' => 'Score Equipe 1'])
            ->add('team2Score', null, ['label' => 'Score Equipe 2'])
            ->add('fans', null, ['label' => 'Affluence'])
            ->add('ffactor1', null, ['label' => 'Mv pop équipe 1'])
            ->add('ffactor2', null, ['label' => 'Mv pop équipe 2'])
            ->add('tv1', null, ['label' => 'Tv équipe 1'])
            ->add('tv2', null, ['label' => 'Tv équipe 2'])
            ->add('Income1', textType::class, ['label' => 'Gains équipe 1'])
            ->add('Income2', textType::class, ['label' => 'Gains équipe 2'])
            ->add(
                'team2',
                entityType::class,
                ['class' => Teams::class, 'choice_label' => 'name', 'label' => 'Equipe 2']
            )
            ->add(
                'fStade',
                entityType::class,
                ['class' => GameDataStadium::class, 'choice_label' => 'type', 'label' => 'Stade']
            )
            ->add(
                'fMeteo',
                entityType::class,
                ['class' => Meteo::class, 'choice_label' => 'nom', 'label' => 'Meteo']
            )
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('matchId')
            ->add('team1.name', null, ['label' => 'Equipe 1'])
            ->add('team1Score', null, ['label' => 'Score Equipe 1'])
            ->add('team2Score', null, ['label' => 'Score Equipe 2'])
            ->add('team2.name', null, ['label' => 'Equipe 2']);
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('matchId')
            ->add('team1.name', null, ['label' => 'Equipe 1'])
            ->add('team1Score', null, ['label' => 'Score Equipe 1'])
            ->add('team2Score', null, ['label' => 'Score Equipe 2'])
            ->add('team2.name', null, ['label' => 'Equipe 2'])
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
