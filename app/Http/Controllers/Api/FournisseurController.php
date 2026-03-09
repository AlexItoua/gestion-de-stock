<?php
// ============================================================
// FournisseurController.php
// ============================================================
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Fournisseur;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FournisseurController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $fournisseurs = Fournisseur::actif()
            ->when($request->search, fn($q) => $q->where('nom', 'like', "%{$request->search}%"))
            ->withCount('produits')
            ->orderBy('nom')
            ->paginate($request->per_page ?? 20);

        return response()->json($fournisseurs);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nom'         => 'required|string|max:255',
            'contact_nom' => 'nullable|string|max:255',
            'telephone'   => 'nullable|string|max:20',
            'email'       => 'nullable|email',
            'adresse'     => 'nullable|string',
            'ville'       => 'nullable|string',
            'pays'        => 'nullable|string',
            'notes'       => 'nullable|string',
        ]);

        $fournisseur = Fournisseur::create($validated);
        return response()->json(['message' => 'Fournisseur créé.', 'fournisseur' => $fournisseur], 201);
    }

    public function show(Fournisseur $fournisseur): JsonResponse
    {
        return response()->json($fournisseur->load('produits:id,nom,code_produit'));
    }

    public function update(Request $request, Fournisseur $fournisseur): JsonResponse
    {
        $validated = $request->validate([
            'nom'         => 'sometimes|string|max:255',
            'contact_nom' => 'nullable|string',
            'telephone'   => 'nullable|string',
            'email'       => 'nullable|email',
            'adresse'     => 'nullable|string',
            'is_active'   => 'boolean',
        ]);
        $fournisseur->update($validated);
        return response()->json(['message' => 'Fournisseur mis à jour.', 'fournisseur' => $fournisseur]);
    }

    public function destroy(Fournisseur $fournisseur): JsonResponse
    {
        $fournisseur->delete();
        return response()->json(['message' => 'Fournisseur supprimé.']);
    }
}
