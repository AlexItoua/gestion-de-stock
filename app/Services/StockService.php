<?php

namespace App\Services;

use App\Models\Stock;
use App\Models\Produit;
use App\Models\MouvementStock;
use App\Models\Alerte;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StockService
{
    /**
     * Ajouter du stock (entrée fournisseur, ajustement)
     */
    public function ajouterStock(
        int    $produitId,
        int    $boutiqueId,
        float  $quantite,
        string $typeMouvement = 'entree',
        string $commentaire = '',
        float  $prixUnitaire = 0,
        ?int   $userId = null
    ): MouvementStock {
        return DB::transaction(function () use ($produitId, $boutiqueId, $quantite, $typeMouvement, $commentaire, $prixUnitaire, $userId) {
            $produit = Produit::findOrFail($produitId);

            // Récupérer ou créer l'enregistrement stock
            $stock = Stock::firstOrCreate(
                ['produit_id' => $produitId, 'boutique_id' => $boutiqueId],
                ['quantite' => 0, 'quantite_detail' => 0, 'valeur_stock' => 0]
            );

            $quantiteAvant = $stock->quantite;
            $stock->ajouter($quantite);

            $mouvement = MouvementStock::create([
                'reference'        => MouvementStock::genererReference(),
                'produit_id'       => $produitId,
                'boutique_id'      => $boutiqueId,
                'user_id'          => $userId ?? Auth::id(),
                'type_mouvement'   => $typeMouvement,
                'quantite'         => $quantite,
                'quantite_avant'   => $quantiteAvant,
                'quantite_apres'   => $stock->quantite,
                'prix_unitaire'    => $prixUnitaire ?: $produit->prix_achat,
                'valeur_totale'    => $quantite * ($prixUnitaire ?: $produit->prix_achat),
                'commentaire'      => $commentaire,
                'date_mouvement'   => now(),
            ]);

            // Résoudre alertes de stock faible si stock remonté
            if ($stock->quantite > $produit->seuil_alerte) {
                Alerte::where('produit_id', $produitId)
                    ->where('boutique_id', $boutiqueId)
                    ->whereIn('type_alerte', ['stock_faible', 'stock_epuise'])
                    ->where('is_resolue', false)
                    ->update(['is_resolue' => true, 'date_resolution' => now()]);
            }

            return $mouvement;
        });
    }

    /**
     * Retirer du stock (vente, perte, ajustement)
     */
    public function retirerStock(
        int    $produitId,
        int    $boutiqueId,
        float  $quantite,
        string $typeMouvement = 'vente',
        string $commentaire = '',
        float  $prixUnitaire = 0,
        ?int   $venteId = null,
        ?int   $userId = null
    ): MouvementStock {
        return DB::transaction(function () use ($produitId, $boutiqueId, $quantite, $typeMouvement, $commentaire, $prixUnitaire, $venteId, $userId) {
            $produit = Produit::findOrFail($produitId);
            $stock = Stock::where('produit_id', $produitId)
                ->where('boutique_id', $boutiqueId)
                ->lockForUpdate()
                ->first();

            if (!$stock || $stock->quantite < $quantite) {
                throw new \Exception(
                    "Stock insuffisant pour {$produit->nom}. " .
                    "Disponible: " . ($stock?->quantite ?? 0) . ", demandé: {$quantite}"
                );
            }

            $quantiteAvant = $stock->quantite;
            $stock->retirer($quantite);

            $mouvement = MouvementStock::create([
                'reference'      => MouvementStock::genererReference(),
                'produit_id'     => $produitId,
                'boutique_id'    => $boutiqueId,
                'user_id'        => $userId ?? Auth::id(),
                'type_mouvement' => $typeMouvement,
                'quantite'       => $quantite,
                'quantite_avant' => $quantiteAvant,
                'quantite_apres' => $stock->quantite,
                'prix_unitaire'  => $prixUnitaire ?: $produit->prix_vente_gros,
                'valeur_totale'  => $quantite * ($prixUnitaire ?: $produit->prix_vente_gros),
                'vente_id'       => $venteId,
                'commentaire'    => $commentaire,
                'date_mouvement' => now(),
            ]);

            // Créer alertes si nécessaire
            $this->verifierEtCreerAlertes($produit, $stock, $boutiqueId);

            return $mouvement;
        });
    }

    /**
     * Transfert de stock entre boutiques
     */
    public function transfererStock(
        int    $produitId,
        int    $boutiqueSourceId,
        int    $boutiqueDestId,
        float  $quantite,
        string $commentaire = '',
        ?int   $userId = null
    ): array {
        return DB::transaction(function () use ($produitId, $boutiqueSourceId, $boutiqueDestId, $quantite, $commentaire, $userId) {
            // Mouvement sortie
            $mouvementSortie = $this->retirerStock(
                $produitId, $boutiqueSourceId, $quantite,
                'transfert_sortie', $commentaire, 0, null, $userId
            );

            // Mouvement entrée
            $mouvementEntree = $this->ajouterStock(
                $produitId, $boutiqueDestId, $quantite,
                'transfert_entree', $commentaire, 0, $userId
            );

            // Lier les deux mouvements
            $mouvementSortie->update([
                'boutique_destination_id' => $boutiqueDestId,
                'mouvement_lie_id'        => $mouvementEntree->id,
            ]);
            $mouvementEntree->update(['mouvement_lie_id' => $mouvementSortie->id]);

            return ['sortie' => $mouvementSortie, 'entree' => $mouvementEntree];
        });
    }

    /**
     * Vérifier et créer des alertes automatiques
     */
    public function verifierEtCreerAlertes(Produit $produit, Stock $stock, int $boutiqueId): void
    {
        // Alerte stock épuisé
        if ($stock->quantite <= 0) {
            $this->creerAlerteSiInexistante($produit, $boutiqueId, 'stock_epuise',
                "Stock épuisé : {$produit->nom}",
                "Le stock de {$produit->nom} est épuisé à la boutique.",
                'danger', $stock->quantite, 0
            );
        }
        // Alerte stock faible
        elseif ($stock->quantite <= $produit->seuil_alerte) {
            $this->creerAlerteSiInexistante($produit, $boutiqueId, 'stock_faible',
                "Stock faible : {$produit->nom}",
                "Le stock de {$produit->nom} est bas ({$stock->quantite} restants, seuil: {$produit->seuil_alerte}).",
                'warning', $stock->quantite, $produit->seuil_alerte
            );
        }
    }

    /**
     * Vérifier l'expiration de tous les produits
     * (à appeler via Scheduler quotidiennement)
     */
    public function verifierExpirations(): void
    {
        $produits = Produit::whereNotNull('date_expiration')
            ->where('is_active', true)
            ->get();

        foreach ($produits as $produit) {
            if ($produit->isExpire()) {
                $this->creerAlerteSiInexistante(
                    $produit, null, 'produit_expire',
                    "Produit expiré : {$produit->nom}",
                    "{$produit->nom} a dépassé sa date d'expiration ({$produit->date_expiration->format('d/m/Y')}).",
                    'danger'
                );
            } elseif ($produit->isExpirationProche()) {
                $jours = $produit->jours_avant_expiration;
                $this->creerAlerteSiInexistante(
                    $produit, null, 'expiration_proche',
                    "Expiration proche : {$produit->nom}",
                    "{$produit->nom} expire dans {$jours} jours ({$produit->date_expiration->format('d/m/Y')}).",
                    'warning', $jours, $produit->jours_alerte_expiration
                );
            }
        }
    }

    private function creerAlerteSiInexistante(
        Produit $produit,
        ?int    $boutiqueId,
        string  $type,
        string  $titre,
        string  $message,
        string  $niveau,
        ?float  $valeurActuelle = null,
        ?float  $valeurSeuil = null
    ): void {
        $existe = Alerte::where('produit_id', $produit->id)
            ->where('boutique_id', $boutiqueId)
            ->where('type_alerte', $type)
            ->where('is_resolue', false)
            ->exists();

        if (!$existe) {
            Alerte::create([
                'produit_id'      => $produit->id,
                'boutique_id'     => $boutiqueId,
                'type_alerte'     => $type,
                'titre'           => $titre,
                'message'         => $message,
                'niveau'          => $niveau,
                'valeur_actuelle' => $valeurActuelle,
                'valeur_seuil'    => $valeurSeuil,
            ]);
        }
    }
}
