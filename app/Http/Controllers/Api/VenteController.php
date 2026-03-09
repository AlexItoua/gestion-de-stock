<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vente;
use App\Services\VenteService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class VenteController extends Controller
{
    public function __construct(private VenteService $venteService) {}

    /**
     * GET /api/ventes
     */
    public function index(Request $request): JsonResponse
    {
        $query = Vente::with(['boutique:id,nom', 'user:id,name', 'details.produit:id,nom'])
            ->orderByDesc('date_vente');

        if ($request->boutique_id)   $query->where('boutique_id', $request->boutique_id);
        if ($request->statut)        $query->where('statut', $request->statut);
        if ($request->date_debut)    $query->whereDate('date_vente', '>=', $request->date_debut);
        if ($request->date_fin)      $query->whereDate('date_vente', '<=', $request->date_fin);
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('numero_vente', 'like', "%{$request->search}%")
                  ->orWhere('nom_client', 'like', "%{$request->search}%");
            });
        }

        // Limiter vendeur à sa boutique
        if ($request->user()->hasRole('vendeur') && $request->user()->boutique_id) {
            $query->where('boutique_id', $request->user()->boutique_id);
        }

        return response()->json($query->paginate($request->per_page ?? 20));
    }

    /**
     * POST /api/ventes
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'boutique_id'       => 'required|exists:boutiques,id',
            'mode_paiement'     => 'required|in:especes,mobile_money,cheque,credit,autre',
            'montant_paye'      => 'nullable|numeric|min:0',
            'nom_client'        => 'nullable|string|max:255',
            'telephone_client'  => 'nullable|string|max:20',
            'notes'             => 'nullable|string',
            'items'             => 'required|array|min:1',
            'items.*.produit_id'    => 'required|exists:produits,id',
            'items.*.quantite'      => 'required|numeric|min:0.001',
            'items.*.type_vente'    => 'required|in:gros,detail',
            'items.*.prix_unitaire' => 'nullable|numeric|min:0',
        ]);

        // Le vendeur ne peut vendre que depuis sa boutique
        if ($request->user()->hasRole('vendeur') && $request->user()->boutique_id) {
            $validated['boutique_id'] = $request->user()->boutique_id;
        }

        try {
            $vente = $this->venteService->creerVente($validated);

            return response()->json([
                'message' => 'Vente enregistrée avec succès.',
                'vente'   => $vente,
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    /**
     * GET /api/ventes/{id}
     */
    public function show(Vente $vente): JsonResponse
    {
        $vente->load(['boutique', 'user:id,name', 'details.produit:id,nom,code_produit,unite_stock']);

        return response()->json(['vente' => $vente]);
    }

    /**
     * POST /api/ventes/{id}/annuler
     */
    public function annuler(Request $request, Vente $vente): JsonResponse
    {
        $request->validate([
            'motif' => 'required|string|max:500',
        ]);

        try {
            $vente = $this->venteService->annulerVente($vente, $request->motif);
            return response()->json([
                'message' => 'Vente annulée. Le stock a été remis à jour.',
                'vente'   => $vente,
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}
