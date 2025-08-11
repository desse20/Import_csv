<?php

namespace App\Http\Controllers;

use App\Services\ContactImportService;
use Illuminate\Http\Request;

class ContactImportController extends Controller
{
    /**
     * Traite la requête d'importation de contacts
     *
     * @param Request $request Requête HTTP
     * @param ContactImportService $importer Service d'importation
     * @return \Illuminate\Http\JsonResponse Réponse JSON structurée
     */
    public function import(Request $request, ContactImportService $importer)
    {
        // Validation manuelle du fichier
        if (!$request->hasFile('file')) {
            return response()->json([
                'error' => 'Aucun fichier fourni'
            ], 400);
        }

        $file = $request->file('file');

        if (!$file->isValid()) {
            return response()->json([
                'error' => 'Fichier invalide'
            ], 400);
        }

        // Vérifier le type MIME
        $allowedMimes = ['text/csv', 'text/plain'];
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            return response()->json([
                'error' => 'Type de fichier non autorisé. Utilisez un fichier CSV.'
            ], 400);
        }

        // TRAITEMENT PAR LE SERVICE
        $result = $importer->import($file->getRealPath());

        // RENVOI DE LA RÉPONSE JSON (FORMAT EXIGÉ PAR LE PDF)
        return response()->json([
            'inserted' => $result['inserted'],
            'skipped' => $result['skipped'],
            'errors' => $result['errors']
        ]);
    }
}
