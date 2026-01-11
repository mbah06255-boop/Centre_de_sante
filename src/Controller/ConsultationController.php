<?php

namespace App\Controller;

use App\Entity\Consultation;
use App\Form\ConsultationType;
use App\Repository\ConsultationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/consultation')]
class ConsultationController extends AbstractController
{
    #[Route('/', name: 'app_consultation_index', methods: ['GET'])]
    public function index(ConsultationRepository $consultationRepository): Response
    {
        return $this->render('consultation/index.html.twig', [
            'consultations' => $consultationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_consultation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $consultation = new Consultation();
        $form = $this->createForm(ConsultationType::class, $consultation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($consultation);
            $entityManager->flush();

            $this->addFlash('success', 'Consultation créée avec succès!');
            return $this->redirectToRoute('app_consultation_index');
        }

        return $this->render('consultation/new.html.twig', [
            'consultation' => $consultation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_consultation_show', methods: ['GET'])]
    public function show(Consultation $consultation): Response
    {
        return $this->render('consultation/show.html.twig', [
            'consultation' => $consultation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_consultation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Consultation $consultation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ConsultationType::class, $consultation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Consultation modifiée avec succès!');
            return $this->redirectToRoute('app_consultation_index');
        }

        return $this->render('consultation/edit.html.twig', [
            'consultation' => $consultation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_consultation_delete', methods: ['POST'])]
    public function delete(Request $request, Consultation $consultation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$consultation->getId(), $request->request->get('_token'))) {
            $entityManager->remove($consultation);
            $entityManager->flush();
            $this->addFlash('success', 'Consultation supprimée avec succès!');
        }

        return $this->redirectToRoute('app_consultation_index');
    }

    // Méthode supplémentaire : Consultations par patient
    #[Route('/patient/{id}', name: 'app_consultation_by_patient', methods: ['GET'])]
    public function consultationsByPatient(int $id, ConsultationRepository $consultationRepository): Response
    {
        $consultations = $consultationRepository->findBy(['patient' => $id], ['dateConsultation' => 'DESC']);
        
        return $this->render('consultation/by_patient.html.twig', [
            'consultations' => $consultations,
        ]);
    }

    // Méthode supplémentaire : Consultations par médecin
    #[Route('/medecin/{id}', name: 'app_consultation_by_medecin', methods: ['GET'])]
    public function consultationsByMedecin(int $id, ConsultationRepository $consultationRepository): Response
    {
        $consultations = $consultationRepository->findBy(['medecin' => $id], ['dateConsultation' => 'DESC']);
        
        return $this->render('consultation/by_medecin.html.twig', [
            'consultations' => $consultations,
        ]);
    }
}