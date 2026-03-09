<?php

namespace App\Services;

use App\Models\Vente;
use App\Models\VenteDetail;
use App\Models\Produit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class VenteService
{
    public function __construct(private StockService $stockService) {}

    /**
     * Créer une vente complète avec déduction du stock automatique
     *
     * @param array $data {
     *   boutique_id, mode_paiement, montant_paye, nom_client?, telephone_client?, notes?,
     *   items: [{produit_id, quantite, type_vente: 'gros'|'detail', prix_unitaire?}]
     * }
     */
    public function creerVente(array $data): Vente
    {
        return DB::transaction(function () use ($data) {
            // 1. Valider le stock disponible avant de commencer
            foreach ($data['items'] as $item) {
                $this->validerDisponibilite($item['produit_id'], $data['boutique_id'], $item['quantite']);
            }

            // 2. Calculer le montant total
            $montantTotal = 0;
            $itemsCalcules = [];

            foreach ($data['items'] as $item) {
                $produit = Produit::findOrFail($item['produit_id']);
                $typeVente = $item['type_vente'] ?? 'gros';

                // Prix selon type de vente
                $prixUnitaire = $item['prix_unitaire']
                    ?? ($typeVente === 'detail' ? $produit->prix_vente_detail : $produit->prix_vente_gros);

                $sousTotal = $item['quantite'] * $prixUnitaire;
                $montantTotal += $sousTotal;

                $itemsCalcules[] = [
                    'produit'      => $produit,
                    'quantite'     => $item['quantite'],
                    'type_vente'   => $typeVente,
                    'prix_unitaire'=> $prixUnitaire,
                    'sous_total'   => $sousTotal,
                ];
            }

            // 3. Créer la vente
            $monnaieRendue = max(0, ($data['montant_paye'] ?? $montantTotal) - $montantTotal);

            $vente = Vente::create([
                'numero_vente'      => Vente::genererNumero(),
                'boutique_id'       => $data['boutique_id'],
                'user_id'           => Auth::id(),
                'montant_total'     => $montantTotal,
                'montant_paye'      => $data['montant_paye'] ?? $montantTotal,
                'monnaie_rendue'    => $monnaieRendue,
                'statut'            => 'finalisee',
                'mode_paiement'     => $data['mode_paiement'] ?? 'especes',
                'nom_client'        => $data['nom_client'] ?? null,
                'telephone_client'  => $data['telephone_client'] ?? null,
                'notes'             => $data['notes'] ?? null,
                'date_vente'        => now(),
            ]);

            // 4. Créer les détails et déduire le stock
            foreach ($itemsCalcules as $item) {
                VenteDetail::create([
                    'vente_id'            => $vente->id,
                    'produit_id'          => $item['produit']->id,
                    'type_vente'          => $item['type_vente'],
                    'quantite'            => $item['quantite'],
                    'prix_unitaire'       => $item['prix_unitaire'],
                    'sous_total'          => $item['sous_total'],
                    'prix_achat_snapshot' => $item['produit']->prix_achat,
                ]);

                // Déduire le stock
                $this->stockService->retirerStock(
                    $item['produit']->id,
                    $data['boutique_id'],
                    $item['quantite'],
                    'vente',
                    "Vente #{$vente->numero_vente}",
                    $item['prix_unitaire'],
                    $vente->id
                );
            }

            return $vente->load('details.produit', 'boutique', 'user');
        });
    }

    /**
     * Annuler une vente et remettre le stock
     */
    public function annulerVente(Vente $vente, string $motif = ''): Vente
    {
        if ($vente->statut === 'annulee') {
            throw new \Exception("Cette vente est déjà annulée.");
        }

        return DB::transaction(function () use ($vente, $motif) {
            foreach ($vente->details as $detail) {
                $this->stockService->ajouterStock(
                    $detail->produit_id,
                    $vente->boutique_id,
                    $detail->quantite,
                    'retour_client',
                    "Annulation vente #{$vente->numero_vente}. {$motif}"
                );
            }

            $vente->update(['statut' => 'annulee', 'notes' => trim($vente->notes . "\nAnnulé: {$motif}")]);

            return $vente->fresh();
        });
    }

    private function validerDisponibilite(int $produitId, int $boutiqueId, float $quantite): void
    {
        $stock = \App\Models\Stock::where('produit_id', $produitId)
            ->where('boutique_id', $boutiqueId)
            ->first();

        if (!$stock || $stock->quantite < $quantite) {
            $produit = Produit::find($produitId);
            throw new \Exception(
                "Stock insuffisant pour \"{$produit->nom}\". " .
                "Disponible: " . ($stock?->quantite ?? 0) . ", demandé: {$quantite}"
            );
        }
    }
}
