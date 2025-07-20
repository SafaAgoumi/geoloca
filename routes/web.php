<?php

use App\Models\Entreprise;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/', function (Request $request) {
    $query = Entreprise::query();

    if ($request->filled('search')) {
        $query->where('nom_entreprise', 'like', '%' . $request->search . '%');
    }

    if ($request->filled('ville')) {
        $query->where('ville', $request->ville);
    }

    if ($request->filled('secteur')) {
        $query->where('secteur', $request->secteur);
    }

    // Pagination pour le tableau
    $entreprises = $query->paginate(10)->withQueryString();

    // Toutes les entreprises filtrées pour la carte (sans pagination)
    $entreprisesCarte = $query->get();

    $villes = Entreprise::select('ville')->distinct()->pluck('ville');
    $secteurs = Entreprise::select('secteur')->distinct()->pluck('secteur');

    return view('welcome', compact('entreprises', 'entreprisesCarte', 'villes', 'secteurs'));
});
