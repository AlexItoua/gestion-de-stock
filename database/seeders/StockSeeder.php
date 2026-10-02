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
        $depot    = Boutique::where('code', 'DEP')->first();
        $comptoir = Boutique::where('code', 'CVT')->first();

        // ⚠️ Quantités calibrées pour déclencher les alertes
        $stocks = [
            // ── Dépôt (stock principal — OK, gros volumes) ───
            ['code' => 'PSC-0001', 'boutique' => $depot,    'quantite' => 120, 'quantite_detail' => 0],
            ['code' => 'PSC-0002', 'boutique' => $depot,    'quantite' => 200, 'quantite_detail' => 0],
            ['code' => 'PSC-0003', 'boutique' => $depot,    'quantite' => 250, 'quantite_detail' => 0],
            ['code' => 'PSC-0004', 'boutique' => $depot,    'quantite' => 90,  'quantite_detail' => 0],

            // ── Comptoir (⚠️ calibré pour alertes) ───
            // PSC-0001 : seuil 10 → 8 cartons = STOCK FAIBLE ✅
            ['code' => 'PSC-0001', 'boutique' => $comptoir, 'quantite' => 8,   'quantite_detail' => 3],
            
            // PSC-0002 : seuil 15 → 60 cartons = OK
            ['code' => 'PSC-0002', 'boutique' => $comptoir, 'quantite' => 60,  'quantite_detail' => 2],
            
            // PSC-0003 : seuil 20 → 15 cartons = STOCK FAIBLE ✅
            ['code' => 'PSC-0003', 'boutique' => $comptoir, 'quantite' => 15,  'quantite_detail' => 4],
            
            // PSC-0004 : seuil 10 → 0 cartons = ÉPUISÉ ✅
            ['code' => 'PSC-0004', 'boutique' => $comptoir, 'quantite' => 0,   'quantite_detail' => 0],
        ];

        foreach ($stocks as $s) {
            $produit = Produit::where('code_produit', $s['code'])->first();
            Stock::updateOrCreate(
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