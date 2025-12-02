<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;

class Step4ConsentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('consentRGPD', CheckboxType::class, [
                'label' => 'J\'accepte le traitement de mes données personnelles conformément au RGPD',
                'required' => true,
                'mapped' => false, // Ce champ n'est pas dans l'entité
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter les conditions RGPD pour continuer.',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}