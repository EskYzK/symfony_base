<?php

namespace App\Form;

use App\Entity\Candidate;
use App\Form\Step1PersonalInfoType;
use App\Form\Step2ExperienceType;
use App\Form\Step3AvailabilityType;
use App\Form\Step4ConsentType;
use Symfony\Component\Form\Flow\AbstractFlowType;
use Symfony\Component\Form\Flow\FormFlowBuilderInterface;
use Symfony\Component\Form\Flow\Type\NavigatorFlowType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CandidateApplicationFlow extends AbstractFlowType
{
    public function buildFormFlow(FormFlowBuilderInterface $builder, array $options): void
    {
        $builder
            ->addStep('personal_info', Step1PersonalInfoType::class)
            ->addStep('experience', Step2ExperienceType::class, [
                'skip' => function (Candidate $candidate) {
                    return !$candidate->hasExperience();
                },
            ])
            ->addStep('availability', Step3AvailabilityType::class)
            ->addStep('consent', Step4ConsentType::class)
            ->addStep('summary', null)
        ;

        $builder->add('navigator', NavigatorFlowType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Candidate::class,
            'step_property_path' => 'currentStep', // Tu devras ajouter ce champ à l'entité
        ]);
    }
}