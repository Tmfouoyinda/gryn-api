<?php

namespace App\Http\Controllers;

use App\Models\Calculation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CalculationController extends Controller
{
    /*
     * Ici on va sauvegarder un nouveau calcul
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'transport.voiture' => 'required|numeric|min:0',
            'transport.train' => 'required|numeric|min:0',
            'transport.bus' => 'required|numeric|min:0',
            'alimentation.regime' => 'required|string|in:Omnivore,Végétarien,Végétalien,Pescetarien',
            'alimentation.kgViande' => 'required|numeric|min:0',
            'alimentation.kgPoisson' => 'required|numeric|min:0',
            'energie.electricite' => 'required|numeric|min:0',
            'energie.gaz' => 'required|numeric|min:0',
            'energie.renouvelable' => 'required|boolean',
            'equipements.nombre' => 'required|string|in:Très peu,Peu,Moyen,Beaucoup',
            'equipements.montant' => 'required|integer|min:0|max:100',
            'emissions.transport' => 'required|numeric',
            'emissions.alimentation' => 'required|numeric',
            'emissions.energie' => 'required|numeric',
            'emissions.consommation' => 'required|numeric',
            'emissions.total' => 'required|numeric',
        ]);

        $calculation = Calculation::create([
            'user_id' => Auth::id(),
            'transport_voiture' => $validated['transport']['voiture'],
            'transport_train' => $validated['transport']['train'],
            'transport_bus' => $validated['transport']['bus'],
            'emissions_transport' => $validated['emissions']['transport'],
            'alimentation_regime' => $validated['alimentation']['regime'],
            'alimentation_kg_viande' => $validated['alimentation']['kgViande'],
            'alimentation_kg_poisson' => $validated['alimentation']['kgPoisson'],
            'emissions_alimentation' => $validated['emissions']['alimentation'],
            'energie_electricite' => $validated['energie']['electricite'],
            'energie_gaz' => $validated['energie']['gaz'],
            'energie_renouvelable' => $validated['energie']['renouvelable'],
            'emissions_energie' => $validated['emissions']['energie'],
            'equipements_nombre' => $validated['equipements']['nombre'],
            'equipements_montant' => $validated['equipements']['montant'],
            'emissions_consommation' => $validated['emissions']['consommation'],
            'total_emissions' => $validated['emissions']['total'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Calcul sauvegardé avec succès',
            'data' => $calculation
        ], 201);
    }

    /*
     * On recupère tous les calculs de l'utilisateur
     */
    public function index()
    {
        $calculations = Calculation::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $calculations
        ]);
    }

    /**
     * On récupere le dernier calcul
     */
    public function latest()
    {
        $calculation = Calculation::where('user_id', Auth::id())
            ->latest()
            ->first();

        return response()->json([
            'success' => true,
            'data' => $calculation
        ]);
    }

    /*
     * On supprime un calcul
     */
    public function destroy($id)
    {
        $calculation = Calculation::where('user_id', Auth::id())
            ->findOrFail($id);
        
        $calculation->delete();

        return response()->json([
            'success' => true,
            'message' => 'Calcul supprimé avec succès'
        ]);
    }
}