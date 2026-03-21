<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vente;
use App\Models\VenteDetail;
use App\Models\Produit;
use App\Models\Boutique;
use App\Models\User;

class VenteSeeder extends Seeder
{
    public function run(): void
    {
        $centrale = Boutique::where('code', 'BCT')->first();
        $admin    = User::where('email', 'admin@gestionstock.com')->first();
        $p1       = Produit::where('code_produit', 'PSC-0001')->first();
        $p2       = Produit::where('code_produit', 'PSC-0002')->first();
        $p3       = Produit::where('code_produit', 'PSC-0003')->first();

        $ventes = [
            // ── Vente aujourd'hui 1 ───────────────────────────────────
            [
                'vente' => [
                    'numero_vente'  => 'VTE-' . now()->format('Y') . '-000010',
                    'boutique_id'   => $centrale->id,
                    'user_id'       => $admin->id,
                    'montant_total' => 8 * $p1->prix_vente_gros,
                    'montant_paye'  => 8 * $p1->prix_vente_gros,
                    'statut'        => 'finalisee',
                    'mode_paiement' => 'especes',
                    'nom_client'    => 'Client Dupont',
                    'date_vente'    => now()->setTime(9, 30),
                ],
                'details' => [
                    [
                        'produit_id'          => $p1->id,
                        'quantite'            => 8,
                        'type_vente'          => 'gros',
                        'prix_unitaire'       => $p1->prix_vente_gros,
                        'prix_achat_snapshot' => $p1->prix_achat,
                        'sous_total'          => 8 * $p1->prix_vente_gros,
                    ],
                ],
            ],

            // ── Vente aujourd'hui 2 ───────────────────────────────────
            [
                'vente' => [
                    'numero_vente'  => 'VTE-' . now()->format('Y') . '-000011',
                    'boutique_id'   => $centrale->id,
                    'user_id'       => $admin->id,
                    'montant_total' => 5 * $p2->prix_vente_gros + 3 * $p3->prix_vente_gros,
                    'montant_paye'  => 5 * $p2->prix_vente_gros + 3 * $p3->prix_vente_gros,
                    'statut'        => 'finalisee',
                    'mode_paiement' => 'mobile_money',
                    'nom_client'    => 'Client Martin',
                    'date_vente'    => now()->setTime(11, 0),
                ],
                'details' => [
                    [
                        'produit_id'          => $p2->id,
                        'quantite'            => 5,
                        'type_vente'          => 'gros',
                        'prix_unitaire'       => $p2->prix_vente_gros,
                        'prix_achat_snapshot' => $p2->prix_achat,
                        'sous_total'          => 5 * $p2->prix_vente_gros,
                    ],
                    [
                        'produit_id'          => $p3->id,
                        'quantite'            => 3,
                        'type_vente'          => 'gros',
                        'prix_unitaire'       => $p3->prix_vente_gros,
                        'prix_achat_snapshot' => $p3->prix_achat,
                        'sous_total'          => 3 * $p3->prix_vente_gros,
                    ],
                ],
            ],

            // ── Vente aujourd'hui 3 ───────────────────────────────────
            [
                'vente' => [
                    'numero_vente'  => 'VTE-' . now()->format('Y') . '-000012',
                    'boutique_id'   => $centrale->id,
                    'user_id'       => $admin->id,
                    'montant_total' => 10 * $p2->prix_vente_gros,
                    'montant_paye'  => 10 * $p2->prix_vente_gros,
                    'statut'        => 'finalisee',
                    'mode_paiement' => 'especes',
                    'nom_client'    => 'Client Mbeki',
                    'date_vente'    => now()->setTime(14, 0),
                ],
                'details' => [
                    [
                        'produit_id'          => $p2->id,
                        'quantite'            => 10,
                        'type_vente'          => 'gros',
                        'prix_unitaire'       => $p2->prix_vente_gros,
                        'prix_achat_snapshot' => $p2->prix_achat,
                        'sous_total'          => 10 * $p2->prix_vente_gros,
                    ],
                ],
            ],
        ];

        foreach ($ventes as $v) {
            $vente = Vente::firstOrCreate(
                ['numero_vente' => $v['vente']['numero_vente']],
                $v['vente']
            );

            foreach ($v['details'] as $detail) {
                VenteDetail::firstOrCreate(
                    [
                        'vente_id'   => $vente->id,
                        'produit_id' => $detail['produit_id'],
                    ],
                    $detail
                );
            }
        }
    }
}
