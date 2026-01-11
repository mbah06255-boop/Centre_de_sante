<?php

namespace App\Controller;

use App\Entity\Medecin;
use App\Form\MedecinType;
use App\Repository\MedecinRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ConsultationRepository;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;

#[Route('/medecin')]
class MedecinController extends AbstractController
{
    #[Route('/', name: 'app_medecin_index', methods: ['GET'])]
    public function index(MedecinRepository $medecinRepository): Response
    {
        return $this->render('medecin/index.html.twig', [
            'medecins' => $medecinRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_medecin_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $medecin = new Medecin();
        $form = $this->createForm(MedecinType::class, $medecin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($medecin);
            $entityManager->flush();

            $this->addFlash('success', 'Médecin créé avec succès!');
            return $this->redirectToRoute('app_medecin_index');
        }

        return $this->render('medecin/new.html.twig', [
            'medecin' => $medecin,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_medecin_show', methods: ['GET'])]
    public function show(Medecin $medecin): Response
    {
        return $this->render('medecin/show.html.twig', [
            'medecin' => $medecin,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_medecin_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Medecin $medecin, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MedecinType::class, $medecin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Médecin modifié avec succès!');
            return $this->redirectToRoute('app_medecin_index');
        }

        return $this->render('medecin/edit.html.twig', [
            'medecin' => $medecin,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_medecin_delete', methods: ['POST'])]
    public function delete(Request $request, Medecin $medecin, EntityManagerInterface $entityManager, ConsultationRepository $consultationRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$medecin->getId(), $request->request->get('_token'))) {
            try {
                // Vérifier si le medecin a des consultations via le repository
                $consultations = $consultationRepository->findBy(['medecin' => $medecin]);
                
                if (count($consultations) > 0) {
                    $this->addFlash('error', 'Impossible de supprimer ce medecin car il a ' . count($consultations) . ' consultation(s) associée(s). Veuillez d\'abord supprimer les consultations.');
                    return $this->redirectToRoute('app_medecin_show', ['id' => $medecin->getId()]);
                }
                
                $entityManager->remove($medecin);
                $entityManager->flush();
                $this->addFlash('success', 'Medecin supprimé avec succès!');
                
            } catch (\Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException $e) {
                $this->addFlash('error', 'Impossible de supprimer ce Medecin car il a des consultations associées.');
                return $this->redirectToRoute('app_medecin_show', ['id' => $medecin->getId()]);
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de la suppression du medecin.');
                return $this->redirectToRoute('app_medecin_show', ['id' => $medecin->getId()]);
            }
        }

        return $this->redirectToRoute('app_medecin_index');
    }
}
