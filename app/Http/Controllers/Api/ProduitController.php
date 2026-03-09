<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Produit;
use App\Models\ModuleStock;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProduitController extends Controller
{
    /**
     * GET /api/produits
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = min($request->per_page ?? 20, 50);

        $query = Produit::query()
            ->with([
                'moduleStock:id,nom,slug',
                'categorie:id,nom',
                'fournisseur:id,nom',
                'stocks:id,produit_id,boutique_id,quantite'
            ])
            ->actif();

        if ($request->module_slug) {
            $query->whereHas('moduleStock', function ($q) use ($request) {
                $q->where('slug', $request->module_slug);
            });
        }

        if ($request->categorie_id) {
            $query->where('categorie_id', $request->categorie_id);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('nom', 'like', "%{$request->search}%")
                  ->orWhere('code_produit', 'like', "%{$request->search}%");
            });
        }

        if ($request->stock_faible) {
            $query->whereHas('stocks', function ($q) {
                $q->whereColumn('quantite', '<=', 'produits.seuil_alerte');
            });
        }

        $produits = $query->orderBy('nom')->paginate($perPage);

        // Bloque les accessors (stock_total, jours_avant_expiration) sur chaque modèle
        $produits->getCollection()->each->setAppends([]);

        // Calcul du stock total manuellement (sans accessor)
        $produits->getCollection()->transform(function ($produit) use ($request) {
            if ($request->boutique_id) {
                $stock = $produit->stocks
                    ->where('boutique_id', $request->boutique_id)
                    ->first();
                $produit->stock_total = $stock ? (float) $stock->quantite : 0;
            } else {
                $produit->stock_total = (float) $produit->stocks->sum('quantite');
            }

            $produit->is_stock_faible = $produit->stock_total <= $produit->seuil_alerte;

            unset($produit->stocks);

            return $produit;
        });

        return response()->json($produits);
    }

    /**
     * POST /api/produits
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nom'                     => 'required|string|max:255',
            'module_stock_id'         => 'required|exists:modules_stock,id',
            'categorie_id'            => 'required|exists:categories,id',
            'fournisseur_id'          => 'nullable|exists:fournisseurs,id',
            'prix_achat'              => 'required|numeric|min:0',
            'prix_vente_gros'         => 'required|numeric|min:0',
            'prix_vente_detail'       => 'nullable|numeric|min:0',
            'unite_stock'             => 'required|in:carton,kg,litre,piece,sac,bouteille',
            'unite_detail'            => 'nullable|in:kg,piece,litre,portion',
            'contenance_carton'       => 'nullable|numeric|min:0',
            'seuil_alerte'            => 'required|integer|min:0',
            'stock_minimum'           => 'required|integer|min:0',
            'date_expiration'         => 'nullable|date|after:today',
            'jours_alerte_expiration' => 'nullable|integer|min:1',
            'description'             => 'nullable|string',
            'vente_detail_possible'   => 'boolean',
        ]);

        $module = ModuleStock::findOrFail($validated['module_stock_id']);
        $validated['code_produit'] = Produit::genererCode($module->slug);

        $produit = Produit::create($validated);

        return response()->json([
            'message' => 'Produit créé avec succès.',
            'produit' => $produit->load([
                'moduleStock:id,nom,slug',
                'categorie:id,nom',
                'fournisseur:id,nom'
            ])
        ], 201);
    }

    /**
     * GET /api/produits/{id}
     */
    public function show(Produit $produit): JsonResponse
    {
        $produit->load([
            'moduleStock:id,nom,slug',
            'categorie:id,nom',
            'fournisseur:id,nom',
            'stocks:id,produit_id,boutique_id,quantite,valeur_stock',
            'stocks.boutique:id,nom'
        ]);

        $stockParBoutique = $produit->stocks->map(function ($stock) {
            return [
                'boutique' => $stock->boutique->nom,
                'quantite' => $stock->quantite,
                'valeur'   => $stock->valeur_stock,
            ];
        });

        $produitData = $produit->setAppends([])->toArray();
        unset($produitData['stocks']);

        return response()->json([
            'produit'            => $produitData,
            'stock_par_boutique' => $stockParBoutique,
        ]);
    }

    /**
     * PUT /api/produits/{id}
     */
    public function update(Request $request, Produit $produit): JsonResponse
    {
        $validated = $request->validate([
            'nom'                     => 'sometimes|string|max:255',
            'fournisseur_id'          => 'nullable|exists:fournisseurs,id',
            'prix_achat'              => 'sometimes|numeric|min:0',
            'prix_vente_gros'         => 'sometimes|numeric|min:0',
            'prix_vente_detail'       => 'nullable|numeric|min:0',
            'unite_stock'             => 'sometimes|in:carton,kg,litre,piece,sac,bouteille',
            'unite_detail'            => 'nullable|in:kg,piece,litre,portion',
            'contenance_carton'       => 'nullable|numeric|min:0',
            'seuil_alerte'            => 'sometimes|integer|min:0',
            'stock_minimum'           => 'sometimes|integer|min:0',
            'date_expiration'         => 'nullable|date',
            'jours_alerte_expiration' => 'nullable|integer|min:1',
            'description'             => 'nullable|string',
            'is_active'               => 'boolean',
            'vente_detail_possible'   => 'boolean',
        ]);

        $produit->update($validated);

        return response()->json([
            'message' => 'Produit mis à jour.',
            'produit' => $produit->fresh([
                'moduleStock:id,nom,slug',
                'categorie:id,nom',
                'fournisseur:id,nom'
            ])
        ]);
    }

    /**
     * DELETE /api/produits/{id}
     */
    public function destroy(Produit $produit): JsonResponse
    {
        if ($produit->stocks()->where('quantite', '>', 0)->exists()) {
            return response()->json([
                'message' => 'Impossible de supprimer un produit avec du stock en cours.'
            ], 422);
        }

        $produit->delete();

        return response()->json(['message' => 'Produit supprimé.']);
    }
}
