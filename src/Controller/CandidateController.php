<?php

namespace App\Controller;

use App\Entity\Candidate;
use App\Form\Step1PersonalInfoType;
use App\Form\Step2ExperienceType;
use App\Form\Step3AvailabilityType;
use App\Form\Step4ConsentType;
use App\Service\FormFlowManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CandidateController extends AbstractController
{
    private const STEPS = [
        'personal_info',
        'experience',
        'availability',
        'consent',
        'summary'
    ];

    #[Route('/apply', name: 'candidate_apply')]
    public function apply(
        Request $request, 
        EntityManagerInterface $entityManager,
        FormFlowManager $flowManager
    ): Response {
        // Initialiser le flow
        $flowManager->initialize('candidate_application', self::STEPS);
        
        $currentStepName = $flowManager->getCurrentStepName();
        $candidate = $this->buildCandidateFromSession($flowManager);

        // Action selon le bouton cliqué
        $action = $request->request->get('action');
        
        if ($action === 'previous') {
            $flowManager->previousStep();
            return $this->redirectToRoute('candidate_apply');
        }

        // Créer le formulaire selon l'étape
        $form = $this->createFormForStep($currentStepName, $candidate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Sauvegarder les données de l'étape
            $this->saveStepData($flowManager, $currentStepName, $candidate, $form);

            // Si c'est la dernière étape, sauvegarder en BDD
            if ($currentStepName === 'summary') {
                $candidate->setStatus('submitted');
                $candidate->setCreatedAt(new \DateTime());
                $candidate->setUpdatedAt(new \DateTime());
                
                $entityManager->persist($candidate);
                $entityManager->flush();
                
                $flowManager->reset();
                $this->addFlash('success', 'Votre candidature a été soumise avec succès !');
                
                return $this->redirectToRoute('candidate_apply');
            }

            // Passer à l'étape suivante
            // Skip l'étape expérience si pas d'expérience
            if ($currentStepName === 'personal_info' && !$candidate->hasExperience()) {
                $flowManager->nextStep(); // Skip experience
            }
            
            $flowManager->nextStep();
            return $this->redirectToRoute('candidate_apply');
        }

        return $this->render('candidate/apply.html.twig', [
            'form' => $form,
            'candidate' => $candidate,
            'currentStep' => $flowManager->getCurrentStepIndex() + 1,
            'totalSteps' => $flowManager->getTotalSteps(),
            'progress' => $flowManager->getProgressPercentage(),
            'isFirstStep' => $flowManager->isFirstStep(),
            'isLastStep' => $currentStepName === 'summary',
            'currentStepName' => $currentStepName,
        ]);
    }

    private function createFormForStep(string $stepName, Candidate $candidate)
    {
        return match($stepName) {
            'personal_info' => $this->createForm(Step1PersonalInfoType::class, $candidate),
            'experience' => $this->createForm(Step2ExperienceType::class, $candidate),
            'availability' => $this->createForm(Step3AvailabilityType::class, $candidate),
            'consent' => $this->createForm(Step4ConsentType::class),
            'summary' => $this->createFormBuilder()->getForm(), // Formulaire vide pour le résumé
            default => throw new \RuntimeException('Unknown step: ' . $stepName)
        };
    }

    private function buildCandidateFromSession(FormFlowManager $flowManager): Candidate
    {
        $allData = $flowManager->getAllData();
        $candidate = new Candidate();

        foreach ($allData as $stepData) {
            foreach ($stepData as $property => $value) {
                $setter = 'set' . ucfirst($property);
                if (method_exists($candidate, $setter)) {
                    $candidate->$setter($value);
                }
            }
        }

        return $candidate;
    }

    private function saveStepData(
        FormFlowManager $flowManager, 
        string $stepName, 
        Candidate $candidate,
        $form
    ): void {
        $data = [];
        
        if ($stepName !== 'summary' && $stepName !== 'consent') {
            foreach ($form->all() as $child) {
                $propertyName = $child->getName();
                $getter = 'get' . ucfirst($propertyName);
                
                if (method_exists($candidate, $getter)) {
                    $data[$propertyName] = $candidate->$getter();
                }
            }
        }
        
        $flowManager->saveStepData($stepName, $data);
    }
}