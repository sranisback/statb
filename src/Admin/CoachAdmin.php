<?php

namespace App\Admin;

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
    protected function configureFormFields(FormMapper $formMapper)
    {
        /** @var Coaches $coach */
        $coach = $this->getSubject();

        $formMapper
            ->add('name', TextType::class)
            ->add(
                'roles',
                ChoiceType::class,
                [
                    'choices' => ['User' => 'USER', 'Admin' => 'ADMIN'],
                    'mapped' => false
                ]
            )
            ->add('passwd', PasswordType::class, ['empty_data'=>$coach->getPasswd(),'required'=>false]);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('name');
        $datagridMapper->add('roles');
      //  $datagridMapper->add('passwd');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('name');
        $listMapper->addIdentifier('roles');
    }

    public function preValidate($object)
    {
        /** @var Coaches $coach */
        $coach = $this->getSubject();

        $role = $this->getForm()->get('roles')->getData();

        $coach->setRoles(['role'=>'ROLE_'.$role]);

        $plainPassword = $object->getPasswd();
        $container = $this->getConfigurationPool()->getContainer();
        if ($container) {
            $encoder = $container->get('security.password_encoder');
            $encoded = $encoder->encodePassword($object, $plainPassword);
            $object->setPasswd($encoded);
        }
    }
}
