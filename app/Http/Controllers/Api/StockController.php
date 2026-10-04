<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use App\Models\MouvementStock;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class StockController extends Controller
{
    public function __construct(private StockService $stockService) {}

    /**
     * GET /api/stocks
     * ──────────────────────────────────────────────────────────────
     * Liste du stock par produit × boutique.
     *
     * Filtres query params :
     *   ?boutique_id=    Filtrer par boutique
     *   ?module_slug=    Filtrer par module (riz-cereales, huiles, ...)
     *   ?categorie_id=   Filtrer par catégorie
     *   ?stock_faible=1  Uniquement les produits sous le seuil d'alerte
     *   ?search=         Recherche par nom ou code produit
     *   ?filter=         in_stock | alert | empty | all (défaut all)
     *   ?per_page=       Nombre par page (max 500, défaut 100)
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) ($request->per_page ?? 100), 500);
        $filter  = $request->input('filter', 'all');

        $query = Stock::query()
            ->with([
                'produit:id,nom,code_produit,seuil_alerte,unite_stock',
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

        // ── Filtres ──────────────────────────────────────────────
        if ($request->filled('boutique_id')) {
            $query->where('boutique_id', $request->boutique_id);
        }

        if ($request->filled('module_slug')) {
            $query->whereHas('produit.moduleStock', function ($q) use ($request) {
                $q->where('slug', $request->module_slug);
            });
        }

        if ($request->filled('categorie_id')) {
            $query->whereHas('produit', function ($q) use ($request) {
                $q->where('categorie_id', $request->categorie_id);
            });
        }

        if ($request->filled('search')) {
            $query->whereHas('produit', function ($q) use ($request) {
                $q->where('nom', 'like', "%{$request->search}%")
                  ->orWhere('code_produit', 'like', "%{$request->search}%");
            });
        }

        // ── Filtres rapides (filter=) ────────────────────────────
        if ($filter === 'in_stock') {
            $query->where('quantite', '>', 0);
        } elseif ($filter === 'alert') {
            $query->whereHas('produit', function ($q) {
                $q->whereColumn('stocks.quantite', '<=', 'produits.seuil_alerte')
                  ->whereColumn('stocks.quantite', '>', 0);
            });
        } elseif ($filter === 'empty') {
            $query->where('quantite', 0);
        }

        if ($request->stock_faible) {
            $query->whereHas('produit', function ($q) {
                $q->whereColumn('stocks.quantite', '<=', 'produits.seuil_alerte');
            });
        }

        // ⭐ TRI : produits avec du stock EN PREMIER
        //     puis par quantité décroissante
        $stocks = $query
            ->orderByRaw('CASE WHEN quantite > 0 THEN 0 ELSE 1 END')  // stock > 0 en premier
            ->orderByDesc('quantite')                                  // puis par quantité
            ->orderBy('produit_id')                                    // puis par produit
            ->paginate($perPage);

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
            'produit_id'  => 'required|exists:produits,id',
            'boutique_id' => 'required|exists:boutiques,id',
            'quantite'    => 'required|numeric',
            'commentaire' => 'required|string|max:500',
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
            'message' => 'Transfert effectué.',
            'sortie'  => $mouvements['sortie'],
            'entree'  => $mouvements['entree'],
        ], 201);
    }

    /**
     * GET /api/stocks/mouvements
     */
    public function mouvements(Request $request): JsonResponse
    {
        $perPage = min($request->per_page ?? 30, 100);

        $query = MouvementStock::with([
                'produit:id,nom,code_produit,unite_stock,module_stock_id',
                'produit.moduleStock:id,nom,slug',
                'boutique:id,nom',
                'user:id,name',
            ])
            ->orderByDesc('date_mouvement');

        if ($request->filled('type_mouvement')) {
            $query->where('type_mouvement', $request->type_mouvement);
        }

        if ($request->filled('boutique_id')) {
            $query->where('boutique_id', $request->boutique_id);
        }

        if ($request->filled('produit_id')) {
            $query->where('produit_id', $request->produit_id);
        }

        if ($request->filled('search')) {
            $query->whereHas('produit', function ($q) use ($request) {
                $q->where('nom', 'like', "%{$request->search}%")
                  ->orWhere('code_produit', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('date_debut')) {
            $query->whereDate('date_mouvement', '>=', $request->date_debut);
        }

        if ($request->filled('date_fin')) {
            $query->whereDate('date_mouvement', '<=', $request->date_fin);
        }

        if ($request->user()->hasRole('vendeur') && $request->user()->boutique_id) {
            $query->where('boutique_id', $request->user()->boutique_id);
        }

        $mouvements = $query->paginate($perPage);

        $today     = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();

        $grouped = $mouvements->getCollection()
            ->groupBy(function ($m) use ($today, $yesterday) {
                $date = Carbon::parse($m->date_mouvement)->toDateString();
                if ($date === $today)     return "Aujourd'hui";
                if ($date === $yesterday) return 'Hier';
                return 'Précédemment';
            })
            ->map(function ($items, $periode) {
                return [
                    'periode'     => $periode,
                    'nb'          => $items->count(),
                    'mouvements'  => $items->map(fn($m) => $this->formaterMouvement($m))->values(),
                ];
            })
            ->values();

        return response()->json([
            'meta' => [
                'current_page' => $mouvements->currentPage(),
                'last_page'    => $mouvements->lastPage(),
                'per_page'     => $mouvements->perPage(),
                'total'        => $mouvements->total(),
            ],
            'grouped' => $grouped,
        ]);
    }

    /**
     * Formate un mouvement pour la réponse API
     */
    private function formaterMouvement(MouvementStock $m): array
    {
        $typesEntree = ['entree', 'transfert_entree', 'ajustement_positif'];
        $estEntree   = in_array($m->type_mouvement, $typesEntree);

        return [
            'id'             => $m->id,
            'type_mouvement' => $m->type_mouvement,
            'est_entree'     => $estEntree,
            'quantite'       => (float) $m->quantite,
            'quantite_affichage' => ($estEntree ? '+' : '-') . number_format(abs($m->quantite), 2, '.', ''),
            'unite'          => $m->produit->unite_stock ?? '',
            'valeur_totale'  => (float) ($m->valeur_totale ?? 0),
            'commentaire'    => $m->commentaire,
            'date_mouvement' => $m->date_mouvement,
            'date_formatee'  => Carbon::parse($m->date_mouvement)->format('d M Y · H:i'),
            'produit' => [
                'id'           => $m->produit->id,
                'nom'          => $m->produit->nom,
                'code_produit' => $m->produit->code_produit,
                'module'       => $m->produit->moduleStock?->nom,
            ],
            'boutique' => [
                'id'  => $m->boutique->id,
                'nom' => $m->boutique->nom,
            ],
            'user' => $m->user ? [
                'id'   => $m->user->id,
                'name' => $m->user->name,
            ] : null,
        ];
    }
}