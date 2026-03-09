<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\{Produit, Vente, Stock, Alerte, MouvementStock, Boutique};
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * GET /api/dashboard
     */
    public function index(Request $request): JsonResponse
    {
        $boutiqueId = $request->boutique_id ?? $request->user()->boutique_id;

        return response()->json([
            'stock'      => $this->statsStock($boutiqueId),
            'mouvements' => $this->statsMouvementsJour($boutiqueId),
            'ventes'     => $this->statsVentes($boutiqueId),
            'alertes'    => $this->statsAlertes($boutiqueId),
            'produits'   => $this->topProduits($boutiqueId),
        ]);
    }

    private function statsStock(?int $boutiqueId): array
    {
        $query = Stock::query();
        if ($boutiqueId) $query->where('boutique_id', $boutiqueId);

        $stocks = $query->with('produit')->get();

        return [
            'total_produits'        => $stocks->count(),
            'valeur_totale'         => round($stocks->sum('valeur_stock'), 2),
            'produits_stock_faible' => $stocks->filter(fn($s) =>
                $s->quantite <= $s->produit->seuil_alerte && $s->quantite > 0
            )->count(),
            'produits_epuises'      => $stocks->where('quantite', '<=', 0)->count(),
        ];
    }

    private function statsMouvementsJour(?int $boutiqueId): array
    {
        $base = MouvementStock::whereDate('date_mouvement', today());
        if ($boutiqueId) $base->where('boutique_id', $boutiqueId);

        // Entrées = entree + ajustement (positif par convention commentaire)
        $entrees = (clone $base)
            ->whereIn('type_mouvement', ['entree', 'transfert_entree'])
            ->sum('quantite');

        // Sorties = vente + perte + transfert_sortie
        $sorties = (clone $base)
            ->whereIn('type_mouvement', ['vente', 'perte', 'transfert_sortie'])
            ->sum('quantite');

        $nb_entrees = (clone $base)
            ->whereIn('type_mouvement', ['entree', 'transfert_entree'])
            ->count();

        $nb_sorties = (clone $base)
            ->whereIn('type_mouvement', ['vente', 'perte', 'transfert_sortie'])
            ->count();

        return [
            'entrees_jour'    => round((float) $entrees, 3),
            'sorties_jour'    => round((float) $sorties, 3),
            'nb_entrees_jour' => $nb_entrees,
            'nb_sorties_jour' => $nb_sorties,
        ];
    }

    private function statsVentes(?int $boutiqueId): array
    {
        $base = Vente::finalisees();
        if ($boutiqueId) $base->where('boutique_id', $boutiqueId);

        $aujourd_hui  = (clone $base)->aujourdhui()->sum('montant_total');
        $semaine      = (clone $base)->cetteSemaine()->sum('montant_total');
        $mois         = (clone $base)->whereMonth('date_vente', now()->month)
                                     ->whereYear('date_vente', now()->year)
                                     ->sum('montant_total');
        $nbVentesJour = (clone $base)->aujourdhui()->count();

        return [
            'aujourd_hui'    => round($aujourd_hui, 2),
            'semaine'        => round($semaine, 2),
            'mois'           => round($mois, 2),
            'nb_ventes_jour' => $nbVentesJour,
        ];
    }

    private function statsAlertes(?int $boutiqueId): array
    {
        $query = Alerte::nonResolues();
        if ($boutiqueId) $query->where('boutique_id', $boutiqueId);

        $alertes = $query->with('produit:id,nom')->orderBy('niveau', 'desc')->limit(10)->get();

        return [
            'total_non_lues' => Alerte::nonLues()
                ->when($boutiqueId, fn($q) => $q->where('boutique_id', $boutiqueId))
                ->count(),
            'stock_faible'   => $alertes->where('type_alerte', 'stock_faible')->count(),
            'stock_epuise'   => $alertes->where('type_alerte', 'stock_epuise')->count(),
            'expirations'    => $alertes->whereIn('type_alerte', ['expiration_proche', 'produit_expire'])->count(),
            'recentes'       => $alertes->take(5)->map(fn($a) => [
                'id'      => $a->id,
                'type'    => $a->type_alerte,
                'titre'   => $a->titre,
                'niveau'  => $a->niveau,
                'produit' => $a->produit?->nom,
                'date'    => $a->created_at->diffForHumans(),
            ]),
        ];
    }

    private function topProduits(?int $boutiqueId): array
    {
        $top = DB::table('ventes_details')
            ->join('ventes', 'ventes.id', '=', 'ventes_details.vente_id')
            ->join('produits', 'produits.id', '=', 'ventes_details.produit_id')
            ->where('ventes.statut', 'finalisee')
            ->whereMonth('ventes.date_vente', now()->month)
            ->when($boutiqueId, fn($q) => $q->where('ventes.boutique_id', $boutiqueId))
            ->select(
                'produits.nom',
                DB::raw('SUM(ventes_details.quantite) as total_quantite'),
                DB::raw('SUM(ventes_details.sous_total) as total_ca')
            )
            ->groupBy('produits.id', 'produits.nom')
            ->orderByDesc('total_quantite')
            ->limit(5)
            ->get();

        $expirations = Produit::whereNotNull('date_expiration')
            ->where('is_active', true)
            ->where('date_expiration', '>', now())
            ->where('date_expiration', '<=', now()->addDays(30))
            ->orderBy('date_expiration')
            ->limit(5)
            ->get(['id', 'nom', 'date_expiration']);

        return [
            'top_ventes'      => $top,
            'bientot_expires' => $expirations->map(fn($p) => [
                'nom'             => $p->nom,
                'date_expiration' => $p->date_expiration->format('d/m/Y'),
                'jours_restants'  => now()->diffInDays($p->date_expiration),
            ]),
        ];
    }
}
