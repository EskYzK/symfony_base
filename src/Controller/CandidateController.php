<?php

namespace App\Controller;

use App\Entity\Candidate;
use App\Form\CandidateApplicationFlow;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CandidateController extends AbstractController
{
    #[Route('/apply', name: 'candidate_apply')]
    public function apply(Request $request, EntityManagerInterface $entityManager): Response
    {
        $candidate = new Candidate();
        $candidate->setStatus('draft');
        $candidate->setCreatedAt(new \DateTime());
        $candidate->setUpdatedAt(new \DateTime());

        $flow = $this->createForm(CandidateApplicationFlow::class, $candidate)
            ->handleRequest($request);

        if ($flow->isSubmitted() && $flow->isValid() && $flow->isFinished()) {
            $candidate->setStatus('submitted');
            $candidate->setUpdatedAt(new \DateTime());

            $entityManager->persist($candidate);
            $entityManager->flush();

            $this->addFlash('success', 'Votre candidature a été soumise avec succès !');

            return $this->redirectToRoute('candidate_apply');
        }

        return $this->render('candidate/apply.html.twig', [
            'form' => $flow->getStepForm(),
            'candidate' => $candidate,
        ]);
    }
}