<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // ─── ÉTAPE 1 : Rôles et permissions ───
            RolePermissionSeeder::class,

            // ─── ÉTAPE 2 : Référentiels de base ───
            BoutiqueSeeder::class,
            UserSeeder::class,

            // ─── ÉTAPE 3 : Référentiels produits ───
            ModuleStockSeeder::class,
            CategorieSeeder::class,
            FournisseurSeeder::class,

            // ─── ÉTAPE 4 : Catalogue produits ───
            ProduitSeeder::class,

            // ─── ÉTAPE 5 : Stocks (VIDES — quantité 0) ───
            StockSeeder::class,

            // ─── ÉTAPE 6 : Historique (VIDES au départ) ───
            MouvementStockSeeder::class,
            VenteSeeder::class,
            AlerteSeeder::class,
        ]);
    }
}