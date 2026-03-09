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
        $centrale = Boutique::where('code', 'BCT')->first();
        $depot    = Boutique::where('code', 'DEP')->first();

        $stocks = [
            ['code' => 'PSC-0001', 'boutique' => $centrale, 'quantite' => 45,  'quantite_detail' => 3],
            ['code' => 'PSC-0001', 'boutique' => $depot,    'quantite' => 120, 'quantite_detail' => 0],
            ['code' => 'PSC-0002', 'boutique' => $centrale, 'quantite' => 60,  'quantite_detail' => 2],
            ['code' => 'PSC-0002', 'boutique' => $depot,    'quantite' => 200, 'quantite_detail' => 0],
            ['code' => 'PSC-0003', 'boutique' => $centrale, 'quantite' => 80,  'quantite_detail' => 4],
            ['code' => 'PSC-0003', 'boutique' => $depot,    'quantite' => 250, 'quantite_detail' => 0],
            ['code' => 'PSC-0004', 'boutique' => $centrale, 'quantite' => 30,  'quantite_detail' => 1],
            ['code' => 'PSC-0004', 'boutique' => $depot,    'quantite' => 90,  'quantite_detail' => 0],
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
