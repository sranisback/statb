<?php


namespace App\Form;

use App\Entity\SpecialRule;
use App\Entity\RacesBb2020;
use App\Entity\Teams;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChooseSpecialRuleType extends AbstractType
{
    private const CHAOS_DWARF = 'Nains du chaos';
    private $valeurAEnlever = ['Bagarre des Terres Arides', 'Super-ligue du Bord du Monde'];

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Teams $equipe */
        $equipe = $options['equipe'];

        /** @var RacesBb2020 $race */
        $race = $equipe->getRace();

        $choices = [];

        /** @var SpecialRule $specialRule */
        foreach ($race->getSpecialRule() as $specialRule) {
            $choices[$specialRule->getName()] = $specialRule->getId();
        }

        if($race->getName() == self::CHAOS_DWARF) {
            foreach ($this->valeurAEnlever as $cle) {
                if (array_key_exists($cle, $choices)) {
                    unset($choices[$cle]);
                }
            }
        }

        if (!empty($race)) {
            $form = $builder->add('regleSpecial', ChoiceType::class, [
                'choices' => $choices,
                'label' => 'choisir la règle spéciale '
            ])
            ->add('teamId', HiddenType::class, [
                'data' => $equipe->getTeamId(),
            ])
            ->add('submit', SubmitType::class, ['label' => 'Ajouter'])
            ->add('cancel', ButtonType::class, ['label' => 'Annuler', 'attr' => ['data-dismiss' => 'modal']]);
        }

        $form->getForm();

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'equipe' => null
            ]
        );
    }
}