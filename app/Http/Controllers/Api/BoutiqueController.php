<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Boutique;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BoutiqueController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Boutique::actif()->withCount('stocks')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nom'         => 'required|string|max:255',
            'code'        => 'required|string|max:10|unique:boutiques',
            'adresse'     => 'nullable|string',
            'ville'       => 'nullable|string',
            'telephone'   => 'nullable|string',
            'responsable' => 'nullable|string',
            'type'        => 'required|in:boutique,depot,entrepot',
        ]);

        $boutique = Boutique::create($validated);
        return response()->json(['message' => 'Boutique créée.', 'boutique' => $boutique], 201);
    }

    public function show(Boutique $boutique): JsonResponse
    {
        $boutique->load('stocks.produit');
        return response()->json($boutique);
    }

    public function update(Request $request, Boutique $boutique): JsonResponse
    {
        $validated = $request->validate([
            'nom'         => 'sometimes|string|max:255',
            'adresse'     => 'nullable|string',
            'telephone'   => 'nullable|string',
            'responsable' => 'nullable|string',
            'is_active'   => 'boolean',
        ]);
        $boutique->update($validated);
        return response()->json(['message' => 'Boutique mise à jour.', 'boutique' => $boutique]);
    }

    public function destroy(Boutique $boutique): JsonResponse
    {
        if ($boutique->ventes()->exists()) {
            return response()->json(['message' => 'Impossible de supprimer une boutique avec des ventes.'], 422);
        }
        $boutique->delete();
        return response()->json(['message' => 'Boutique supprimée.']);
    }
}
