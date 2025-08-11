<?php

namespace App\Services;

use App\Models\Contact;
use Illuminate\Support\Facades\Log;

class ContactImportService
{
    /**
     * Importe un fichier CSV de contacts dans la base de données
     *
     * @param string $filePath Chemin absolu vers le fichier CSV
     * @return array Résultats de l'importation [inserted, skipped, errors]
     */
    public function import(string $filePath)
    {
        // Ouvrir le fichier CSV en mode lecture
        $file = fopen($filePath, 'r');

        // Lire et ignorer la première ligne (en-têtes des colonnes)
        fgetcsv($file);

        // Initialiser les variables de résultat
        $results = [
            'inserted' => 0, // Nombre de contacts insérés
            'skipped' => 0,  // Nombre de lignes ignorées
            'errors' => []   // Détail des erreurs
        ];

        $lineNumber = 1;     // Compteur de lignes (commence après l'en-tête)
        $processedEmails = []; // Stocke les emails traités pour détecter les doublons dans le fichier

        // Boucle de lecture ligne par ligne du fichier CSV
        while (($row = fgetcsv($file)) !== false) {
            $lineNumber++;

            // Convertir la ligne CSV en tableau associatif
            $data = $this->mapRow($row);

            // Valider la ligne et récupérer les erreurs éventuelles
            if ($error = $this->validateRow($data, $lineNumber, $processedEmails)) {
                // Enregistrer l'erreur et passer à la ligne suivante
                $results['errors'][] = $error;
                $results['skipped']++;
                continue;
            }

            // Tentative d'insertion dans la base de données
            try {
                Contact::create($data);
                $results['inserted']++;
                $processedEmails[] = $data['email']; // Mémoriser l'email traité
            } catch (\Exception $e) {
                // Gestion des erreurs d'insertion imprévues
                $results['skipped']++;
                $results['errors'][] = [
                    'line' => $lineNumber,
                    'message' => 'Erreur lors de l\'insertion: ' . $e->getMessage()
                ];
                Log::error("Erreur d'insertion ligne $lineNumber: " . $e->getMessage());
            }
        }

        // Fermer le fichier après traitement
        fclose($file);

        return $results;
    }

    /**
     * Convertit une ligne CSV en tableau associatif
     *
     * @param array $row Ligne CSV brute
     * @return array Données structurées [first_name, last_name, email, phone]
     */
    private function mapRow(array $row): array
    {
        // Mapping des colonnes selon l'ordre spécifié dans le PDF
        return [
            'first_name' => $row[0] ?? '',      // Colonne 1: Prénom
            'last_name'  => $row[1] ?? '',      // Colonne 2: Nom
            'email'      => $row[2] ?? '',      // Colonne 3: Email
            'phone'      => $row[3] ?? null     // Colonne 4: Téléphone (optionnel)
        ];
    }

    /**
     * Valide une ligne de données selon les règles métier
     *
     * @param array $data Données à valider
     * @param int $line Numéro de ligne dans le CSV
     * @param array $processedEmails Emails déjà traités dans ce fichier
     * @return array|null Message d'erreur ou null si valide
     */
    private function validateRow(array $data, int $line, array &$processedEmails): ?array
    {
        // Vérification des champs obligatoires (first_name, last_name, email)
        if (empty($data['first_name']) || empty($data['last_name']) || empty($data['email'])) {
            return [
                'line' => $line,
                'message' => 'Champs obligatoires manquants'
            ];
        }

        // Validation du format de l'email
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return [
                'line' => $line,
                'message' => 'Format email invalide'
            ];
        }

        // Vérification des doublons dans la base de données
        if (Contact::where('email', $data['email'])->exists()) {
            return [
                'line' => $line,
                'message' => 'Email déjà existant en base'
            ];
        }

        // Vérification des doublons dans le fichier courant
        if (in_array($data['email'], $processedEmails)) {
            return [
                'line' => $line,
                'message' => 'Doublon dans le fichier'
            ];
        }

        return null; // Aucune erreur détectée
    }
}
