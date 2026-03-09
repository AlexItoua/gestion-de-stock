<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Categorie;
use App\Models\ModuleStock;

class CategorieSeeder extends Seeder
{
    public function run(): void
    {
        $module = ModuleStock::where('slug', 'poisson-sale')->first();

        $categories = [
            ['nom' => 'Carton 10kg', 'slug' => 'carton-10kg'],
            ['nom' => 'Carton 5kg',  'slug' => 'carton-5kg'],
            ['nom' => 'Carton 3kg',  'slug' => 'carton-3kg'],
        ];

        foreach ($categories as $cat) {
            Categorie::firstOrCreate(['slug' => $cat['slug']], [
                'nom'             => $cat['nom'],
                'module_stock_id' => $module->id,
                'is_active'       => true,
            ]);
        }
    }
}
