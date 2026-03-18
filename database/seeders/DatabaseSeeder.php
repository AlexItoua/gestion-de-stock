<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            BoutiqueSeeder::class,
            UserSeeder::class,
            FournisseurSeeder::class,
            ModuleStockSeeder::class,
            CategorieSeeder::class,
            ProduitSeeder::class,
            StockSeeder::class,
            MouvementStockSeeder::class,
        ]);
    }
}
