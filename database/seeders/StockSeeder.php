<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Stock;
use App\Models\Produit;
use App\Models\Boutique;

class StockSeeder extends Seeder
{
    /**
     * ⚠️ Ce seeder crée UNIQUEMENT les lignes de stock avec QUANTITÉ = 0.
     *    Le stock réel sera ajouté quand le client fera ses ENTREES.
     */
    public function run(): void
    {
        $depot    = Boutique::where('code', 'DEP')->first();
        $boutique = Boutique::where('code', 'GAREL')->first();

        $produits = Produit::all();

        foreach ($produits as $produit) {
            Stock::updateOrCreate(
                ['produit_id' => $produit->id, 'boutique_id' => $depot->id],
                [
                    'quantite'        => 0,
                    'quantite_detail' => 0,
                    'valeur_stock'    => 0,
                ]
            );

            Stock::updateOrCreate(
                ['produit_id' => $produit->id, 'boutique_id' => $boutique->id],
                [
                    'quantite'        => 0,
                    'quantite_detail' => 0,
                    'valeur_stock'    => 0,
                ]
            );
        }
    }
}