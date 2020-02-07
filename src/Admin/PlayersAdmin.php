<?php

namespace App\Admin;

use App\Entity\Coaches;
use App\Entity\GameDataPlayers;
use App\Entity\Players;
use App\Entity\PlayersIcons;
use App\Entity\Races;
use App\Entity\Teams;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

final class PlayersAdmin extends AbstractAdmin
{
    public function preValidate($object): void
    {
        /** @var Players $joueur */
        $joueur = $this->getSubject();

        $container = $this->getConfigurationPool()->getContainer();

        if (!empty($container)) {
            $entityManager = $container->get('doctrine.orm.entity_manager');
        }

        if (!empty($entityManager)) {
            $listeIcones = $entityManager
                ->getRepository(PlayersIcons::class)
                ->toutesLesIconesDunePosition($joueur->getFPos());


            if ($listeIcones) {
                $joueur->setIcon($listeIcones[rand(0, count($listeIcones) - 1)]);
            } else {
                /** @var PlayersIcons $icone */
                $icone = $entityManager->getRepository(PlayersIcons::class)->findOneBy(['iconName' => 'nope']);
                $joueur->setIcon($icone);
            }
        }
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->add('nr', null, ['label' => 'Numéro'])
            ->add('name', null, ['label' => 'Nom'])
            ->add('fCid', EntityType::class, ['class' => Coaches::class, 'choice_label' => 'name', 'label' => 'Coach'])
            ->add('fRid', EntityType::class, ['class' => Races::class, 'choice_label' => 'name', 'label' => 'Roster'])
            ->add(
                'fPos',
                EntityType::class,
                ['class' => GameDataPlayers::class, 'choice_label' => 'pos', 'label' => 'Position']
            )
            ->add(
                'ownedByTeam',
                EntityType::class,
                ['class' => Teams::class, 'choice_label' => 'name', 'label' => 'Equipe']
            )
            ->add('type', ChoiceType::class, ['choices' => ['Oui' => 2, 'Non' => 1], 'label' => 'Journalier ? '])
            ->add('status', ChoiceType::class, ['choices' => ['Ok' => 1, 'Vendu' => 7, 'Mort' => 8, 'XP' => 9]])
            ->add('achMa', null, ['label' => 'Bonus Mv'])
            ->add('achSt', null, ['label' => 'Bonus Fo'])
            ->add('achAg', null, ['label' => 'Bonus Ag'])
            ->add('achAv', null, ['label' => 'Bonus Av'])
            ->add('extraSpp', null, ['label' => 'Bonus Xp'])
            ->add('extraVal', null, ['label' => 'Bonus Valeur'])
            ->add('injMa', null, ['label' => 'Blessure Mv'])
            ->add('injSt', null, ['label' => 'Blessure Fo'])
            ->add('injAg', null, ['label' => 'Blessure Ag'])
            ->add('injAv', null, ['label' => 'Blessure Av'])
            ->add('injRpm', null, ['label' => 'RPM']);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('name', null, ['label' => 'nom'])
            ->add('ownedByTeam.name', null, ['label' => 'Equipe'])
            ->add('ownedByTeam.year', null, ['label' => 'Année'])
            ->add('fRid.name', null, ['label' => 'Race'])
            ->add('fCid.name', null, ['label' => 'Coach'])
            ->add('status');
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('nr', null, ['label' => 'Numero', 'editable' => true])
            ->add('name', null, ['label' => 'nom', 'editable' => true])
            ->add('ownedByTeam.name', null, ['label' => 'Equipe'])
            ->add('fRid.name', null, ['label' => 'Race'])
            ->add('fPos.pos', null, ['label' => 'Position'])
            ->add('fCid.name', null, ['label' => 'Coach'])
            ->add('status', null, ['editable' => true])
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
