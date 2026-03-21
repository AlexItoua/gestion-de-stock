<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MouvementStock;
use App\Models\Stock;
use App\Models\Produit;
use App\Models\Boutique;
use App\Models\User;

class MouvementStockSeeder extends Seeder
{
    public function run(): void
    {
        $centrale = Boutique::where('code', 'BCT')->first();
        $depot    = Boutique::where('code', 'DEP')->first();
        $admin    = User::where('email', 'admin@gestionstock.com')->first();

        $p1 = Produit::where('code_produit', 'PSC-0001')->first();
        $p2 = Produit::where('code_produit', 'PSC-0002')->first();
        $p3 = Produit::where('code_produit', 'PSC-0003')->first();
        $p4 = Produit::where('code_produit', 'PSC-0004')->first();

        // ── Toujours relatif à maintenant ────────────────────────────
        $today     = now();
        $hier      = now()->subDay();
        $avantHier = now()->subDays(2);
        $semaine   = now()->subDays(5);

        $getQte = function ($produit, $boutique) {
            $stock = Stock::where('produit_id', $produit->id)
                ->where('boutique_id', $boutique->id)
                ->first();
            return $stock ? (float) $stock->quantite : 0;
        };

        $mouvements = [

            // ── AUJOURD'HUI : entrées ──────────────────────────────────
            [
                'produit_id'     => $p1->id,
                'boutique_id'    => $depot->id,
                'user_id'        => $admin->id,
                'type_mouvement' => 'entree',
                'quantite'       => 30,
                'quantite_avant' => $getQte($p1, $depot),
                'quantite_apres' => $getQte($p1, $depot) + 30,
                'prix_unitaire'  => $p1->prix_achat,
                'valeur_totale'  => 30 * $p1->prix_achat,
                'commentaire'    => 'Réception fournisseur',
                'date_mouvement' => $today->copy()->setTime(8, 30),
            ],
            [
                'produit_id'     => $p2->id,
                'boutique_id'    => $depot->id,
                'user_id'        => $admin->id,
                'type_mouvement' => 'entree',
                'quantite'       => 50,
                'quantite_avant' => $getQte($p2, $depot),
                'quantite_apres' => $getQte($p2, $depot) + 50,
                'prix_unitaire'  => $p2->prix_achat,
                'valeur_totale'  => 50 * $p2->prix_achat,
                'commentaire'    => 'Réception fournisseur',
                'date_mouvement' => $today->copy()->setTime(9, 0),
            ],
            [
                'produit_id'     => $p3->id,
                'boutique_id'    => $centrale->id,
                'user_id'        => $admin->id,
                'type_mouvement' => 'entree',
                'quantite'       => 25,
                'quantite_avant' => $getQte($p3, $centrale),
                'quantite_apres' => $getQte($p3, $centrale) + 25,
                'prix_unitaire'  => $p3->prix_achat,
                'valeur_totale'  => 25 * $p3->prix_achat,
                'commentaire'    => 'Transfert dépôt',
                'date_mouvement' => $today->copy()->setTime(10, 15),
            ],

            // ── AUJOURD'HUI : sorties ──────────────────────────────────
            [
                'produit_id'     => $p1->id,
                'boutique_id'    => $centrale->id,
                'user_id'        => $admin->id,
                'type_mouvement' => 'vente',
                'quantite'       => 8,
                'quantite_avant' => $getQte($p1, $centrale),
                'quantite_apres' => max(0, $getQte($p1, $centrale) - 8),
                'prix_unitaire'  => $p1->prix_vente_gros,
                'valeur_totale'  => 8 * $p1->prix_vente_gros,
                'commentaire'    => 'Vente client',
                'date_mouvement' => $today->copy()->setTime(11, 0),
            ],
            [
                'produit_id'     => $p2->id,
                'boutique_id'    => $centrale->id,
                'user_id'        => $admin->id,
                'type_mouvement' => 'vente',
                'quantite'       => 12,
                'quantite_avant' => $getQte($p2, $centrale),
                'quantite_apres' => max(0, $getQte($p2, $centrale) - 12),
                'prix_unitaire'  => $p2->prix_vente_gros,
                'valeur_totale'  => 12 * $p2->prix_vente_gros,
                'commentaire'    => 'Vente client',
                'date_mouvement' => $today->copy()->setTime(14, 30),
            ],
            [
                'produit_id'     => $p4->id,
                'boutique_id'    => $centrale->id,
                'user_id'        => $admin->id,
                'type_mouvement' => 'perte',
                'quantite'       => 2,
                'quantite_avant' => $getQte($p4, $centrale),
                'quantite_apres' => max(0, $getQte($p4, $centrale) - 2),
                'prix_unitaire'  => $p4->prix_achat,
                'valeur_totale'  => 2 * $p4->prix_achat,
                'commentaire'    => 'Produit endommagé',
                'date_mouvement' => $today->copy()->setTime(15, 0),
            ],

            // ── HIER ──────────────────────────────────────────────────
            [
                'produit_id'     => $p1->id,
                'boutique_id'    => $depot->id,
                'user_id'        => $admin->id,
                'type_mouvement' => 'entree',
                'quantite'       => 60,
                'quantite_avant' => $getQte($p1, $depot),
                'quantite_apres' => $getQte($p1, $depot) + 60,
                'prix_unitaire'  => $p1->prix_achat,
                'valeur_totale'  => 60 * $p1->prix_achat,
                'commentaire'    => 'Réception fournisseur',
                'date_mouvement' => $hier->copy()->setTime(9, 0),
            ],
            [
                'produit_id'     => $p3->id,
                'boutique_id'    => $centrale->id,
                'user_id'        => $admin->id,
                'type_mouvement' => 'vente',
                'quantite'       => 15,
                'quantite_avant' => $getQte($p3, $centrale),
                'quantite_apres' => max(0, $getQte($p3, $centrale) - 15),
                'prix_unitaire'  => $p3->prix_vente_gros,
                'valeur_totale'  => 15 * $p3->prix_vente_gros,
                'commentaire'    => 'Vente client',
                'date_mouvement' => $hier->copy()->setTime(16, 0),
            ],

            // ── AVANT-HIER ────────────────────────────────────────────
            [
                'produit_id'     => $p2->id,
                'boutique_id'    => $depot->id,
                'user_id'        => $admin->id,
                'type_mouvement' => 'entree',
                'quantite'       => 100,
                'quantite_avant' => $getQte($p2, $depot),
                'quantite_apres' => $getQte($p2, $depot) + 100,
                'prix_unitaire'  => $p2->prix_achat,
                'valeur_totale'  => 100 * $p2->prix_achat,
                'commentaire'    => 'Réception fournisseur',
                'date_mouvement' => $avantHier->copy()->setTime(8, 0),
            ],
            [
                'produit_id'     => $p4->id,
                'boutique_id'    => $centrale->id,
                'user_id'        => $admin->id,
                'type_mouvement' => 'vente',
                'quantite'       => 20,
                'quantite_avant' => $getQte($p4, $centrale),
                'quantite_apres' => max(0, $getQte($p4, $centrale) - 20),
                'prix_unitaire'  => $p4->prix_vente_gros,
                'valeur_totale'  => 20 * $p4->prix_vente_gros,
                'commentaire'    => 'Vente client',
                'date_mouvement' => $avantHier->copy()->setTime(11, 0),
            ],

            // ── IL Y A 5 JOURS ────────────────────────────────────────
            [
                'produit_id'     => $p1->id,
                'boutique_id'    => $depot->id,
                'user_id'        => $admin->id,
                'type_mouvement' => 'entree',
                'quantite'       => 200,
                'quantite_avant' => $getQte($p1, $depot),
                'quantite_apres' => $getQte($p1, $depot) + 200,
                'prix_unitaire'  => $p1->prix_achat,
                'valeur_totale'  => 200 * $p1->prix_achat,
                'commentaire'    => 'Stock initial semaine',
                'date_mouvement' => $semaine->copy()->setTime(8, 0),
            ],
            [
                'produit_id'     => $p2->id,
                'boutique_id'    => $centrale->id,
                'user_id'        => $admin->id,
                'type_mouvement' => 'ajustement',
                'quantite'       => 10,
                'quantite_avant' => $getQte($p2, $centrale),
                'quantite_apres' => $getQte($p2, $centrale) + 10,
                'prix_unitaire'  => 0,
                'valeur_totale'  => 0,
                'commentaire'    => 'Correction inventaire',
                'date_mouvement' => $semaine->copy()->setTime(10, 0),
            ],
        ];

        foreach ($mouvements as $data) {
            MouvementStock::firstOrCreate(
                [
                    'produit_id'     => $data['produit_id'],
                    'boutique_id'    => $data['boutique_id'],
                    'type_mouvement' => $data['type_mouvement'],
                    'quantite'       => $data['quantite'],
                    'date_mouvement' => $data['date_mouvement'],
                ],
                array_merge($data, [
                    'reference' => MouvementStock::genererReference()
                ])
            );
        }
    }
}
