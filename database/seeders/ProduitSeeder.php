<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Produit;
use App\Models\ModuleStock;
use App\Models\Categorie;
use App\Models\Fournisseur;

class ProduitSeeder extends Seeder
{
    public function run(): void
    {
        $module = ModuleStock::where('slug', 'poisson-sale')->first();
        $cat10  = Categorie::where('slug', 'carton-10kg')->first();
        $cat5   = Categorie::where('slug', 'carton-5kg')->first();
        $cat3   = Categorie::where('slug', 'carton-3kg')->first();
        $f1     = Fournisseur::where('nom', 'Importateurs Congo SARL')->first();
        $f2     = Fournisseur::where('nom', 'Atlantic Fish Trading')->first();

        $produits = [
            [
                'code_produit'          => 'PSC-0001',
                'nom'                   => 'Poisson Salé Carton 10kg Grade A',
                'module_stock_id'       => $module->id,
                'categorie_id'          => $cat10->id,
                'fournisseur_id'        => $f1->id,
                'prix_achat'            => 18000,
                'prix_vente_gros'       => 22000,
                'prix_vente_detail'     => 2500,
                'unite_stock'           => 'carton',
                'unite_detail'          => 'kg',
                'contenance_carton'     => 10,
                'seuil_alerte'          => 10,
                'stock_minimum'         => 5,
                'vente_detail_possible' => true,
            ],
            [
                'code_produit'          => 'PSC-0002',
                'nom'                   => 'Poisson Salé Carton 5kg',
                'module_stock_id'       => $module->id,
                'categorie_id'          => $cat5->id,
                'fournisseur_id'        => $f1->id,
                'prix_achat'            => 9500,
                'prix_vente_gros'       => 12000,
                'prix_vente_detail'     => 2500,
                'unite_stock'           => 'carton',
                'unite_detail'          => 'kg',
                'contenance_carton'     => 5,
                'seuil_alerte'          => 15,
                'stock_minimum'         => 8,
                'vente_detail_possible' => true,
            ],
            [
                'code_produit'          => 'PSC-0003',
                'nom'                   => 'Poisson Salé Carton 3kg',
                'module_stock_id'       => $module->id,
                'categorie_id'          => $cat3->id,
                'fournisseur_id'        => $f2->id,
                'prix_achat'            => 5500,
                'prix_vente_gros'       => 7000,
                'prix_vente_detail'     => 2400,
                'unite_stock'           => 'carton',
                'unite_detail'          => 'kg',
                'contenance_carton'     => 3,
                'seuil_alerte'          => 20,
                'stock_minimum'         => 10,
                'vente_detail_possible' => true,
            ],
            [
                'code_produit'          => 'PSC-0004',
                'nom'                   => 'Poisson Salé Carton 10kg Grade B',
                'module_stock_id'       => $module->id,
                'categorie_id'          => $cat10->id,
                'fournisseur_id'        => $f2->id,
                'prix_achat'            => 15000,
                'prix_vente_gros'       => 18500,
                'prix_vente_detail'     => 2000,
                'unite_stock'           => 'carton',
                'unite_detail'          => 'kg',
                'contenance_carton'     => 10,
                'seuil_alerte'          => 10,
                'stock_minimum'         => 5,
                'vente_detail_possible' => true,
            ],
        ];

        foreach ($produits as $data) {
            Produit::firstOrCreate(['code_produit' => $data['code_produit']], $data);
        }
    }
}
