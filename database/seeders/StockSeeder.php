<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Stock;
use App\Models\Produit;
use App\Models\Boutique;

class StockSeeder extends Seeder
{
    public function run(): void
    {
        $depot    = Boutique::where('code', 'DEP')->first(); // Dépôt Poisson Salé
        $comptoir = Boutique::where('code', 'CVT')->first(); // Comptoir de Vente

        $stocks = [
            // ── Dépôt (stock en gros — cartons reçus fournisseur) ─────
            ['code' => 'PSC-0001', 'boutique' => $depot,    'quantite' => 120, 'quantite_detail' => 0],
            ['code' => 'PSC-0002', 'boutique' => $depot,    'quantite' => 200, 'quantite_detail' => 0],
            ['code' => 'PSC-0003', 'boutique' => $depot,    'quantite' => 250, 'quantite_detail' => 0],
            ['code' => 'PSC-0004', 'boutique' => $depot,    'quantite' => 90,  'quantite_detail' => 0],

            // ── Comptoir (stock transféré pour vente gros + détail) ───
            ['code' => 'PSC-0001', 'boutique' => $comptoir, 'quantite' => 45,  'quantite_detail' => 3],
            ['code' => 'PSC-0002', 'boutique' => $comptoir, 'quantite' => 60,  'quantite_detail' => 2],
            ['code' => 'PSC-0003', 'boutique' => $comptoir, 'quantite' => 80,  'quantite_detail' => 4],
            ['code' => 'PSC-0004', 'boutique' => $comptoir, 'quantite' => 30,  'quantite_detail' => 1],
        ];

        foreach ($stocks as $s) {
            $produit = Produit::where('code_produit', $s['code'])->first();
            Stock::firstOrCreate(
                ['produit_id' => $produit->id, 'boutique_id' => $s['boutique']->id],
                [
                    'quantite'        => $s['quantite'],
                    'quantite_detail' => $s['quantite_detail'],
                    'valeur_stock'    => $s['quantite'] * $produit->prix_achat,
                ]
            );
        }
    }
}
