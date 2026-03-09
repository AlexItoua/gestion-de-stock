<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ModuleStock;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class ModuleStockController extends Controller
{
    /**
     * GET /api/modules
     */
    public function index(): JsonResponse
    {
        $modules = ModuleStock::withCount(['produits', 'categories'])
            ->actif()
            ->get();

        return response()->json($modules);
    }

    /**
     * POST /api/modules
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nom'         => 'required|string|max:255',
            'description' => 'nullable|string',
            'icone'       => 'nullable|string|max:50',
            'couleur'     => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'ordre'       => 'nullable|integer|min:0',
        ]);

        $validated['slug'] = Str::slug($validated['nom']);

        if (ModuleStock::where('slug', $validated['slug'])->exists()) {
            return response()->json(['message' => 'Un module avec ce nom existe déjà.'], 422);
        }

        $module = ModuleStock::create($validated);

        return response()->json([
            'message' => 'Module créé. Vous pouvez maintenant y ajouter des catégories et produits.',
            'module'  => $module,
        ], 201);
    }

    /**
     * PUT /api/modules/{id}
     */
    public function update(Request $request, ModuleStock $moduleStock): JsonResponse
    {
        $validated = $request->validate([
            'nom'         => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'icone'       => 'nullable|string|max:50',
            'couleur'     => 'nullable|string|max:7',
            'ordre'       => 'nullable|integer|min:0',
            'is_active'   => 'boolean',
        ]);

        $moduleStock->update($validated);

        return response()->json(['message' => 'Module mis à jour.', 'module' => $moduleStock]);
    }
}
