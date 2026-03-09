<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class CategorieController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $categories = Categorie::with('moduleStock:id,nom,slug')
            ->actif()
            ->when($request->module_id, fn($q) => $q->where('module_stock_id', $request->module_id))
            ->withCount('produits')
            ->orderBy('nom')
            ->get();

        return response()->json($categories);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'module_stock_id' => 'required|exists:modules_stock,id',
            'nom'             => 'required|string|max:255',
            'description'     => 'nullable|string',
        ]);
        $validated['slug'] = Str::slug($validated['nom'] . '-' . uniqid());

        $categorie = Categorie::create($validated);
        return response()->json(['message' => 'Catégorie créée.', 'categorie' => $categorie->load('moduleStock')], 201);
    }

    public function update(Request $request, Categorie $categorie): JsonResponse
    {
        $validated = $request->validate([
            'nom'         => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'is_active'   => 'boolean',
        ]);
        $categorie->update($validated);
        return response()->json(['message' => 'Catégorie mise à jour.', 'categorie' => $categorie]);
    }

    public function destroy(Categorie $categorie): JsonResponse
    {
        if ($categorie->produits()->exists()) {
            return response()->json(['message' => 'Catégorie utilisée par des produits.'], 422);
        }
        $categorie->delete();
        return response()->json(['message' => 'Catégorie supprimée.']);
    }
}
