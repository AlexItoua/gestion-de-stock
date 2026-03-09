<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Alerte;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AlerteController extends Controller
{
    public function __construct(private StockService $stockService) {}

    /**
     * GET /api/alertes
     */
    public function index(Request $request): JsonResponse
    {
        $query = Alerte::with(['produit:id,nom,code_produit', 'boutique:id,nom'])
            ->orderByRaw("FIELD(niveau, 'danger', 'warning', 'info')")
            ->orderByDesc('created_at');

        if ($request->non_resolues) $query->nonResolues();
        if ($request->non_lues)     $query->nonLues();
        if ($request->type)         $query->where('type_alerte', $request->type);
        if ($request->boutique_id)  $query->where('boutique_id', $request->boutique_id);

        $alertes = $query->paginate($request->per_page ?? 20);

        return response()->json([
            'alertes'          => $alertes,
            'total_non_lues'   => Alerte::nonLues()->count(),
            'total_non_resolues' => Alerte::nonResolues()->count(),
        ]);
    }

    /**
     * PUT /api/alertes/{id}/lire
     */
    public function marquerLue(Alerte $alerte): JsonResponse
    {
        $alerte->marquerLue();
        return response()->json(['message' => 'Alerte marquée comme lue.']);
    }

    /**
     * PUT /api/alertes/lire-toutes
     */
    public function marquerToutesLues(Request $request): JsonResponse
    {
        Alerte::nonLues()
            ->when($request->boutique_id, fn($q) => $q->where('boutique_id', $request->boutique_id))
            ->update(['is_lue' => true]);

        return response()->json(['message' => 'Toutes les alertes marquées comme lues.']);
    }

    /**
     * PUT /api/alertes/{id}/resoudre
     */
    public function resoudre(Alerte $alerte): JsonResponse
    {
        $alerte->resoudre();
        return response()->json(['message' => 'Alerte résolue.']);
    }

    /**
     * POST /api/alertes/verifier
     * Déclencher manuellement la vérification des alertes
     */
    public function verifier(): JsonResponse
    {
        $this->stockService->verifierExpirations();
        return response()->json(['message' => 'Vérification des alertes effectuée.']);
    }
}
