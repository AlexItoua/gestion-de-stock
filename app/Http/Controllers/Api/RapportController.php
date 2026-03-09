<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\{Vente, VenteDetail, MouvementStock, Stock, Produit};
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class RapportController extends Controller
{
    /**
     * GET /api/rapports/ventes-jour
     */
    public function ventesJour(Request $request): JsonResponse
    {
        $date       = $request->date ?? now()->toDateString();
        $boutiqueId = $request->boutique_id;

        $query = Vente::with(['details.produit:id,nom', 'user:id,name', 'boutique:id,nom'])
            ->finalisees()
            ->whereDate('date_vente', $date);

        if ($boutiqueId) $query->where('boutique_id', $boutiqueId);

        $ventes = $query->get();

        return response()->json([
            'date'         => $date,
            'nb_ventes'    => $ventes->count(),
            'total_ca'     => round($ventes->sum('montant_total'), 2),
            'ventes'       => $ventes,
            'par_boutique' => $ventes->groupBy('boutique.nom')->map(fn($v, $b) => [
                'boutique' => $b,
                'nb'       => $v->count(),
                'total'    => round($v->sum('montant_total'), 2),
            ])->values(),
        ]);
    }

    /**
     * GET /api/rapports/ventes-mois
     */
    public function ventesMois(Request $request): JsonResponse
    {
        $mois       = $request->mois ?? now()->month;
        $annee      = $request->annee ?? now()->year;
        $boutiqueId = $request->boutique_id;

        // Ventes par jour du mois
        $parJour = DB::table('ventes')
            ->where('statut', 'finalisee')
            ->whereMonth('date_vente', $mois)
            ->whereYear('date_vente', $annee)
            ->when($boutiqueId, fn($q) => $q->where('boutique_id', $boutiqueId))
            ->select(
                DB::raw('DATE(date_vente) as jour'),
                DB::raw('COUNT(*) as nb_ventes'),
                DB::raw('SUM(montant_total) as total')
            )
            ->groupBy('jour')
            ->orderBy('jour')
            ->get();

        $totalMois = $parJour->sum('total');

        // Top produits du mois
        $topProduits = DB::table('ventes_details')
            ->join('ventes', 'ventes.id', '=', 'ventes_details.vente_id')
            ->join('produits', 'produits.id', '=', 'ventes_details.produit_id')
            ->where('ventes.statut', 'finalisee')
            ->whereMonth('ventes.date_vente', $mois)
            ->whereYear('ventes.date_vente', $annee)
            ->when($boutiqueId, fn($q) => $q->where('ventes.boutique_id', $boutiqueId))
            ->select(
                'produits.nom',
                'produits.unite_stock',
                DB::raw('SUM(ventes_details.quantite) as quantite_vendue'),
                DB::raw('SUM(ventes_details.sous_total) as chiffre_affaires')
            )
            ->groupBy('produits.id', 'produits.nom', 'produits.unite_stock')
            ->orderByDesc('quantite_vendue')
            ->limit(10)
            ->get();

        return response()->json([
            'mois'         => $mois,
            'annee'        => $annee,
            'total_ca'     => round($totalMois, 2),
            'par_jour'     => $parJour,
            'top_produits' => $topProduits,
        ]);
    }

    /**
     * GET /api/rapports/etat-stock
     */
    public function etatStock(Request $request): JsonResponse
    {
        $boutiqueId = $request->boutique_id;

        $stocks = Stock::with(['produit.moduleStock:id,nom', 'produit.categorie:id,nom', 'boutique:id,nom'])
            ->when($boutiqueId, fn($q) => $q->where('boutique_id', $boutiqueId))
            ->join('produits', 'produits.id', '=', 'stocks.produit_id')
            ->select('stocks.*')
            ->orderBy('produits.nom')
            ->get();

        $valeurTotale = $stocks->sum('valeur_stock');

        $parModule = $stocks->groupBy('produit.moduleStock.nom')->map(fn($items, $module) => [
            'module'       => $module,
            'nb_produits'  => $items->count(),
            'valeur'       => round($items->sum('valeur_stock'), 2),
            'stock_faible' => $items->filter(fn($s) =>
                $s->quantite <= $s->produit->seuil_alerte && $s->quantite > 0
            )->count(),
        ])->values();

        return response()->json([
            'date'          => now()->toDateString(),
            'valeur_totale' => round($valeurTotale, 2),
            'par_module'    => $parModule,
            'stocks'        => $stocks->map(fn($s) => [
                'produit'     => $s->produit->nom,
                'code'        => $s->produit->code_produit,
                'module'      => $s->produit->moduleStock->nom,
                'boutique'    => $s->boutique->nom,
                'quantite'    => $s->quantite,
                'unite'       => $s->produit->unite_stock,
                'valeur'      => round($s->valeur_stock, 2),
                'seuil'       => $s->produit->seuil_alerte,
                'statut'      => $s->quantite <= 0 ? 'epuise'
                    : ($s->quantite <= $s->produit->seuil_alerte ? 'faible' : 'ok'),
            ]),
        ]);
    }

    /**
     * GET /api/rapports/pertes
     */
    public function pertes(Request $request): JsonResponse
    {
        $request->validate([
            'date_debut' => 'required|date',
            'date_fin'   => 'required|date|after_or_equal:date_debut',
        ]);

        $pertes = MouvementStock::with(['produit:id,nom,code_produit', 'boutique:id,nom', 'user:id,name'])
            ->where('type_mouvement', 'perte')
            ->parPeriode($request->date_debut, $request->date_fin)
            ->when($request->boutique_id, fn($q) => $q->where('boutique_id', $request->boutique_id))
            ->orderByDesc('date_mouvement')
            ->get();

        return response()->json([
            'periode'       => ['debut' => $request->date_debut, 'fin' => $request->date_fin],
            'nb_pertes'     => $pertes->count(),
            'valeur_totale' => round($pertes->sum('valeur_totale'), 2),
            'pertes'        => $pertes,
        ]);
    }
}
