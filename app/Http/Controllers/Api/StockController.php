<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use App\Models\MouvementStock;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class StockController extends Controller
{
    public function __construct(private StockService $stockService) {}

    /**
     * GET /api/stocks
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = min($request->per_page ?? 20, 50);

        $query = Stock::query()
            ->with([
                'produit:id,nom,code_produit,seuil_alerte',  // ← simplifié, sans sous-relations
                'boutique:id,nom,code'
            ])
            ->select([
                'id',
                'produit_id',
                'boutique_id',
                'quantite',
                'quantite_detail',
                'valeur_stock'
            ]);

        if ($request->boutique_id) {
            $query->where('boutique_id', $request->boutique_id);
        }

        if ($request->module_slug) {
            $query->whereHas('produit.moduleStock', function ($q) use ($request) {
                $q->where('slug', $request->module_slug);
            });
        }

        if ($request->categorie_id) {
            $query->whereHas('produit', function ($q) use ($request) {
                $q->where('categorie_id', $request->categorie_id);
            });
        }

        if ($request->stock_faible) {
            $query->whereHas('produit', function ($q) {
                $q->whereColumn('stocks.quantite', '<=', 'produits.seuil_alerte');
            });
        }

        if ($request->search) {
            $query->whereHas('produit', function ($q) use ($request) {
                $q->where('nom', 'like', "%{$request->search}%")
                  ->orWhere('code_produit', 'like', "%{$request->search}%");
            });
        }

        $stocks = $query->orderByDesc('id')->paginate($perPage);

        return response()->json($stocks);
    }

    /**
     * POST /api/stocks/entree
     */
    public function entree(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'produit_id'    => 'required|exists:produits,id',
            'boutique_id'   => 'required|exists:boutiques,id',
            'quantite'      => 'required|numeric|min:0.001',
            'prix_unitaire' => 'nullable|numeric|min:0',
            'commentaire'   => 'nullable|string|max:500',
        ]);

        $mouvement = $this->stockService->ajouterStock(
            $validated['produit_id'],
            $validated['boutique_id'],
            $validated['quantite'],
            'entree',
            $validated['commentaire'] ?? '',
            $validated['prix_unitaire'] ?? 0
        );

        $stock = Stock::where('produit_id', $validated['produit_id'])
            ->where('boutique_id', $validated['boutique_id'])
            ->select(['id', 'produit_id', 'boutique_id', 'quantite', 'valeur_stock'])
            ->first();

        return response()->json([
            'message'   => 'Stock ajouté avec succès.',
            'mouvement' => $mouvement->load([
                'produit:id,nom,code_produit',
                'boutique:id,nom',
                'user:id,name'
            ]),
            'stock' => $stock
        ], 201);
    }

    /**
     * POST /api/stocks/perte
     */
    public function perte(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'produit_id'  => 'required|exists:produits,id',
            'boutique_id' => 'required|exists:boutiques,id',
            'quantite'    => 'required|numeric|min:0.001',
            'commentaire' => 'required|string|max:500',
        ]);

        $mouvement = $this->stockService->retirerStock(
            $validated['produit_id'],
            $validated['boutique_id'],
            $validated['quantite'],
            'perte',
            $validated['commentaire']
        );

        return response()->json([
            'message'   => 'Perte enregistrée.',
            'mouvement' => $mouvement->load([
                'produit:id,nom,code_produit',
                'boutique:id,nom',
                'user:id,name'
            ]),
        ], 201);
    }

    /**
     * POST /api/stocks/ajustement
     */
    public function ajustement(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'produit_id'    => 'required|exists:produits,id',
            'boutique_id'   => 'required|exists:boutiques,id',
            'quantite'      => 'required|numeric',
            'commentaire'   => 'required|string|max:500',
        ]);

        $quantite = $validated['quantite'];
        $type = $quantite >= 0 ? 'ajustement_positif' : 'ajustement_negatif';

        $mouvement = $quantite >= 0
            ? $this->stockService->ajouterStock(
                $validated['produit_id'],
                $validated['boutique_id'],
                abs($quantite),
                $type,
                $validated['commentaire']
            )
            : $this->stockService->retirerStock(
                $validated['produit_id'],
                $validated['boutique_id'],
                abs($quantite),
                $type,
                $validated['commentaire']
            );

        return response()->json([
            'message'   => 'Ajustement effectué.',
            'mouvement' => $mouvement->load([
                'produit:id,nom,code_produit',
                'boutique:id,nom',
                'user:id,name'
            ]),
        ], 201);
    }

    /**
     * POST /api/stocks/transfert
     */
    public function transfert(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'produit_id'          => 'required|exists:produits,id',
            'boutique_source_id'  => 'required|exists:boutiques,id',
            'boutique_dest_id'    => 'required|exists:boutiques,id|different:boutique_source_id',
            'quantite'            => 'required|numeric|min:0.001',
            'commentaire'         => 'nullable|string|max:500',
        ]);

        $mouvements = $this->stockService->transfererStock(
            $validated['produit_id'],
            $validated['boutique_source_id'],
            $validated['boutique_dest_id'],
            $validated['quantite'],
            $validated['commentaire'] ?? ''
        );

        return response()->json([
            'message'  => 'Transfert effectué.',
            'sortie'   => $mouvements['sortie'],
            'entree'   => $mouvements['entree'],
        ], 201);
    }

    /**
     * GET /api/stocks/mouvements
     */
    public function mouvements(Request $request): JsonResponse
    {
        $perPage = min($request->per_page ?? 30, 50);

        $query = MouvementStock::with([
            'produit:id,nom,code_produit',
            'boutique:id,nom,code',
            'user:id,name'
        ])->orderByDesc('date_mouvement');

        if ($request->boutique_id) {
            $query->where('boutique_id', $request->boutique_id);
        }

        if ($request->produit_id) {
            $query->where('produit_id', $request->produit_id);
        }

        if ($request->type_mouvement) {
            $query->where('type_mouvement', $request->type_mouvement);
        }

        if ($request->date_debut) {
            $query->whereDate('date_mouvement', '>=', $request->date_debut);
        }

        if ($request->date_fin) {
            $query->whereDate('date_mouvement', '<=', $request->date_fin);
        }

        return response()->json($query->paginate($perPage));
    }
}
