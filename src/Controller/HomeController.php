<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\PatientRepository;
use App\Repository\MedecinRepository;
use App\Repository\ConsultationRepository;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
     public function index(
        PatientRepository $patientRepository,
        MedecinRepository $medecinRepository,
        ConsultationRepository $consultationRepository
    ): Response
    {
        return $this->render('home/index.html.twig', [
            'patients' => $patientRepository->count([]),
            'medecins' => $medecinRepository->count([]),
            'consultations' => $consultationRepository->count([]),
        ]);
    }
}
