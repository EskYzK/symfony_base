<?php

namespace App\Form;

use App\Entity\Candidate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Step3AvailabilityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('availabilityDate', DateType::class, [
                'label' => 'Date de disponibilité',
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('immediatelyAvailable', CheckboxType::class, [
                'label' => 'Disponible immédiatement',
                'required' => false,
                'mapped' => false, // Ce champ n'est pas dans l'entité
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Candidate::class,
        ]);
    }
}