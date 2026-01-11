<?php

// src/Controller/PatientController.php

namespace App\Controller;

use App\Entity\Patient;
use App\Entity\Consultation;
use App\Form\PatientType;
use App\Repository\PatientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ConsultationRepository;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;

#[Route('/patient')]
class PatientController extends AbstractController
{
    #[Route('/', name: 'app_patient_index', methods: ['GET'])]
    public function index(PatientRepository $patientRepository): Response
    {
        return $this->render('patient/index.html.twig', [
            'patients' => $patientRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_patient_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $patient = new Patient();
        $form = $this->createForm(PatientType::class, $patient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($patient);
            $entityManager->flush();

            $this->addFlash('success', 'Patient créé avec succès!');
            return $this->redirectToRoute('app_patient_index');
        }

        return $this->render('patient/new.html.twig', [
            'patients' => $patient,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_patient_show', methods: ['GET'])]
    public function show(Patient $patient): Response
    {
        return $this->render('patient/show.html.twig', [
            'patient' => $patient,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_patient_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Patient $patient, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PatientType::class, $patient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Patient modifié avec succès!');
            return $this->redirectToRoute('app_patient_index');
        }

        return $this->render('patient/edit.html.twig', [
            'patient' => $patient,
            'form' => $form,
        ]);
    }

   #[Route('/{id}', name: 'app_patient_delete', methods: ['POST'])]
    public function delete(Request $request, Patient $patient, EntityManagerInterface $entityManager, ConsultationRepository $consultationRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$patient->getId(), $request->request->get('_token'))) {
            try {
                // Vérifier si le patient a des consultations via le repository
                $consultations = $consultationRepository->findBy(['patient' => $patient]);
                
                if (count($consultations) > 0) {
                    $this->addFlash('error', 'Impossible de supprimer ce patient car il a ' . count($consultations) . ' consultation(s) associée(s). Veuillez d\'abord supprimer les consultations.');
                    return $this->redirectToRoute('app_patient_show', ['id' => $patient->getId()]);
                }
                
                $entityManager->remove($patient);
                $entityManager->flush();
                $this->addFlash('success', 'Patient supprimé avec succès!');
                
            } catch (\Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException $e) {
                $this->addFlash('error', 'Impossible de supprimer ce patient car il a des consultations associées.');
                return $this->redirectToRoute('app_patient_show', ['id' => $patient->getId()]);
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de la suppression du patient.');
                return $this->redirectToRoute('app_patient_show', ['id' => $patient->getId()]);
            }
        }

        return $this->redirectToRoute('app_patient_index');
    }
}