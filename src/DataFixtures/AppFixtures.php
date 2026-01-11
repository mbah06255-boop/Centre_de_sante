<?php

namespace App\DataFixtures;

use App\Entity\Patient;
use App\Entity\Medecin;
use App\Entity\Consultation;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR'); // Faker en français

        // Tableaux pour stocker les entités créées
        $patients = [];
        $medecins = [];

        // 1. Créer 20 Patients
        echo "Création des patients...\n";
        for ($i = 0; $i < 20; $i++) {
            $patient = new Patient();
            $patient->setNom($faker->lastName());
            $patient->setPrenom($faker->firstName());
            $patient->setDateNaissance($faker->dateTimeBetween('-80 years', '-18 years'));
            $patient->setAdresse($faker->address());
            $patient->setTelephone($faker->phoneNumber());
            
            
            $manager->persist($patient);
            $patients[] = $patient;
        }
        
        echo "✓ 20 patients créés\n";

        // 2. Créer 10 Médecins
        echo "Création des médecins...\n";
        
        $specialites = [
            'Médecin Généraliste',
            'Cardiologue',
            'Dermatologue',
            'Pédiatre',
            'Gynécologue',
            'Ophtalmologue',
            'ORL',
            'Psychiatre',
            'Dentiste',
            'Chirurgien'
        ];

        for ($i = 0; $i < 10; $i++) {
            $medecin = new Medecin();
            $medecin->setNom($faker->lastName());
            $medecin->setSpecialite($specialites[$i]);
            $medecin->setTelephone($faker->phoneNumber());
            $medecin->setEmail($faker->email());
            
            $manager->persist($medecin);
            $medecins[] = $medecin;
        }
        
        echo "✓ 10 médecins créés\n";

        // 3. Créer 50 Consultations
        echo "Création des consultations...\n";
        
        $motifs = [
            'Consultation de routine',
            'Douleurs abdominales',
            'Maux de tête persistants',
            'Contrôle annuel',
            'Fièvre et toux',
            'Problèmes de peau',
            'Douleurs articulaires',
            'Fatigue chronique',
            'Troubles du sommeil',
            'Anxiété et stress'
        ];

        $diagnostics = [
            'Rien de grave, repos conseillé',
            'Infection virale bénigne',
            'Hypertension artérielle',
            'Allergie saisonnière',
            'Gastrite légère',
            'Migraine',
            'Arthrose débutante',
            'Anémie ferriprive',
            'Insomnie',
            'État grippal'
        ];

        $traitements = [
            'Paracétamol 1g 3x/jour pendant 5 jours',
            'Ibuprofène 400mg selon besoin',
            'Antibiotique : Amoxicilline 1g 2x/jour pendant 7 jours',
            'Antihistaminique + corticoïde nasal',
            'Repos et hydratation',
            'Anti-inflammatoire + kinésithérapie',
            'Supplémentation en fer',
            'Mélatonine + hygiène du sommeil',
            'Vitamine D + calcium',
            'Crème hydratante + protection solaire'
        ];

        for ($i = 0; $i < 50; $i++) {
            $consultation = new Consultation();
            
            // Date aléatoire dans les 6 derniers mois
            $consultation->setDate($faker->dateTimeBetween('-6 months', 'now'));
            
            // Associer un patient et un médecin aléatoires
            $consultation->setPatient($faker->randomElement($patients));
            $consultation->setMedecin($faker->randomElement($medecins));
            
            // Motif, diagnostic et traitement aléatoires
            $consultation->setMotif($faker->randomElement($motifs));
            $consultation->setDiagnostic($faker->randomElement($diagnostics));
            
            $manager->persist($consultation);
        }
        
        echo "✓ 50 consultations créées\n";

        // Sauvegarder toutes les données
        $manager->flush();
        
        echo "\n✓✓✓ Toutes les données ont été insérées avec succès! ✓✓✓\n";
    }
}