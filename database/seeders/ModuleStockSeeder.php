<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ModuleStock;

class ModuleStockSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            ['nom' => 'Poisson Salé',  'slug' => 'poisson-sale', 'icone' => '🐟', 'couleur' => '#0ea5e9', 'is_active' => true,  'ordre' => 1],
            ['nom' => 'Boulangerie',   'slug' => 'boulangerie',  'icone' => '🍞', 'couleur' => '#f59e0b', 'is_active' => false, 'ordre' => 2],
            ['nom' => 'Boissons',      'slug' => 'boissons',     'icone' => '🥤', 'couleur' => '#10b981', 'is_active' => false, 'ordre' => 3],
            ['nom' => 'Cave',          'slug' => 'cave',         'icone' => '🍷', 'couleur' => '#8b5cf6', 'is_active' => false, 'ordre' => 4],
        ];

        foreach ($modules as $module) {
            ModuleStock::firstOrCreate(['slug' => $module['slug']], $module);
        }
    }
}
