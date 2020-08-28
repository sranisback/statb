<?php

namespace App\Admin_old;

use App\Entity\Coaches;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class CoachAdmin extends AbstractAdmin
{
    public function preValidate($object): void
    {
        /** @var Coaches $coach */
        $coach = $this->getSubject();

        $role = $this->getForm()->get('roles')->getData();

        $coach->setRoles(['role' => 'ROLE_'.$role]);

        /** @var Coaches $object */
        $plainPassword = $object->getPasswd();
        $container = $this->getConfigurationPool()->getContainer();
        if ($container !== null) {
            $encoder = $container->get('security.password_encoder');
            $encoded = $encoder->encodePassword($object, $plainPassword);
            $object->setPasswd($encoded);
        }
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        /** @var Coaches $coach */
        $coach = $this->getSubject();

        $formMapper
            ->add('name', TextType::class, ['label' => 'Nom'])
            ->add(
                'roles',
                ChoiceType::class,
                [
                    'choices' => ['Utilisateur' => 'USER', 'Admin' => 'ADMIN'],
                    'mapped' => false,
                    'label' => 'Role',
                ]
            )
            ->add(
                'passwd',
                PasswordType::class,
                ['empty_data' => $coach->getPasswd(), 'required' => false, 'label' => 'Mot de passe']
            );
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper->add('name', null, ['label' => 'Nom']);
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper->addIdentifier('name', null, ['label' => 'Nom'])
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
