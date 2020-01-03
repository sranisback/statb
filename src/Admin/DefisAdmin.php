<?php

namespace App\Admin;

use App\Entity\Defis;
use App\Entity\Matches;
use App\Entity\Teams;
use DateTime;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class DefisAdmin extends AbstractAdmin
{
    public function preValidate($object)
    {
        /** @var Defis $defis */
        $defis = $this->getSubject();

        if (empty($defis->getDateDefi())) {
            $dateDefis = DateTime::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s"));
            if (!empty($dateDefis)) {
                $defis->setDateDefi($dateDefis);
            } else {
                throw new \Exception('probleme datetime');
            }
        }
    }
    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add(
                'equipeOrigine',
                entityType::class,
                [
                    'class' => Teams::class,
                    'choice_label' => 'name',
                    'label' => 'Equipe defieuse',
                    'group_by' => 'ownedByCoach.name',
                ]
            )
            ->add(
                'equipeDefiee',
                entityType::class,
                [
                    'class' => Teams::class,
                    'choice_label' => 'name',
                    'label' => 'Equipe defiee',
                    'group_by' => 'ownedByCoach.name',
                ]
            )
            ->add('dateDefi')
            ->add(
                'matchDefi',
                entityType::class,
                [
                    'class' => Matches::class,
                    'choice_label' => function ($matches) {
                        return $matches->getTeam1()->getName().' VS '.$matches->getTeam2()->getName(
                        ).', Annee : '.$matches->getTeam1()->getYear();
                    },
                    'label' => 'Match défie',
                ]
            )
            ->add('defieRealise');
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('equipeOrigine.name', null, ['label' => 'Equipe defieuse'])
            ->add('equipeDefiee.name', null, ['label' => 'Equipe defiee'])
            ->add('defieRealise', null, ['label' => 'Fait']);
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id', null, ['label' => 'id'])
            ->add('equipeOrigine.name', null, ['label' => 'Equipe defieuse'])
            ->add('equipeDefiee.name', null, ['label' => 'Equipe defiee'])
            ->add('defieRealise', null, ['label' => 'Fait'])
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
