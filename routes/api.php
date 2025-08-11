<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactImportController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Ici vous pouvez enregistrer vos routes API. Ces routes sont chargées par
| le RouteServiceProvider dans un groupe middleware "api".
|
*/

// Route pour l'importation de contacts (POST /api/contacts/import)
Route::post('/contacts/import', [ContactImportController::class, 'import'])->name('contacts.import');
