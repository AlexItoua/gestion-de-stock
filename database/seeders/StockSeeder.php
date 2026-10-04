<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Stock;
use App\Models\Produit;
use App\Models\Boutique;

class StockSeeder extends Seeder
{
    /**
     * Au démarrage : on crée UNIQUEMENT les lignes de stock pour
     * "Garel Shop" (le seul local actuel).
     *
     * Le jour où le client ouvre un Dépôt, on ajoutera les lignes
     * pour DEP au fur et à mesure.
     */
    public function run(): void
    {
        $boutique = Boutique::where('code', 'GAREL')->first();

        $produits = Produit::all();

        foreach ($produits as $produit) {
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