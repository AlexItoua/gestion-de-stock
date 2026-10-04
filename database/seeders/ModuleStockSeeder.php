<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ModuleStock;

class ModuleStockSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            ['nom' => 'Riz & Céréales',       'slug' => 'riz-cereales',     'icone' => '🌾', 'couleur' => '#f59e0b', 'is_active' => true, 'ordre' => 1],
            ['nom' => 'Huiles & Corps gras',  'slug' => 'huiles',           'icone' => '🫒', 'couleur' => '#eab308', 'is_active' => true, 'ordre' => 2],
            ['nom' => 'Sucre & Farine',       'slug' => 'sucre-farine',     'icone' => '🍚', 'couleur' => '#ec4899', 'is_active' => true, 'ordre' => 3],
            ['nom' => 'Conserves & Pâtes',    'slug' => 'conserves',        'icone' => '🥫', 'couleur' => '#dc2626', 'is_active' => true, 'ordre' => 4],
            ['nom' => 'Légumes frais',        'slug' => 'legumes',          'icone' => '🧅', 'couleur' => '#16a34a', 'is_active' => true, 'ordre' => 5],
            ['nom' => 'Boissons',             'slug' => 'boissons',         'icone' => '🥤', 'couleur' => '#0ea5e9', 'is_active' => true, 'ordre' => 6],
            ['nom' => 'Produits laitiers',    'slug' => 'produits-laitiers','icone' => '🥛', 'couleur' => '#6366f1', 'is_active' => true, 'ordre' => 7],
            ['nom' => 'Hygiène & Entretien',  'slug' => 'hygiene',          'icone' => '🧼', 'couleur' => '#8b5cf6', 'is_active' => true, 'ordre' => 8],
            ['nom' => 'Condiments & Épices',  'slug' => 'condiments',       'icone' => '🧂', 'couleur' => '#ef4444', 'is_active' => true, 'ordre' => 9],
            ['nom' => 'Snacks & Biscuits',    'slug' => 'snacks',           'icone' => '🍪', 'couleur' => '#f97316', 'is_active' => true, 'ordre' => 10],
        ];

        foreach ($modules as $module) {
            ModuleStock::updateOrCreate(['slug' => $module['slug']], $module);
        }
    }
}