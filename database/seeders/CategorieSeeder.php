<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Categorie;
use App\Models\ModuleStock;

class CategorieSeeder extends Seeder
{
    public function run(): void
    {
        $mRiz        = ModuleStock::where('slug', 'riz-cereales')->first();
        $mHuiles     = ModuleStock::where('slug', 'huiles')->first();
        $mSucre      = ModuleStock::where('slug', 'sucre-farine')->first();
        $mConserves  = ModuleStock::where('slug', 'conserves')->first();
        $mLegumes    = ModuleStock::where('slug', 'legumes')->first();
        $mBoissons   = ModuleStock::where('slug', 'boissons')->first();
        $mLaitiers   = ModuleStock::where('slug', 'produits-laitiers')->first();
        $mHygiene    = ModuleStock::where('slug', 'hygiene')->first();
        $mCondiments = ModuleStock::where('slug', 'condiments')->first();
        $mSnacks     = ModuleStock::where('slug', 'snacks')->first();

        $categories = [
            // ─── Riz & Céréales ───
            ['nom' => 'Riz en sac 50kg',      'slug' => 'riz-sac-50kg',       'module' => $mRiz],
            ['nom' => 'Riz en sac 25kg',      'slug' => 'riz-sac-25kg',       'module' => $mRiz],
            ['nom' => 'Riz en sachet 5kg',    'slug' => 'riz-sachet-5kg',     'module' => $mRiz],
            ['nom' => 'Maïs & Mil',           'slug' => 'mais-mil',           'module' => $mRiz],

            // ─── Huiles ───
            ['nom' => 'Bidon 5L',             'slug' => 'huile-bidon-5l',     'module' => $mHuiles],
            ['nom' => 'Bidon 1L',             'slug' => 'huile-bidon-1l',     'module' => $mHuiles],
            ['nom' => 'Sachet 500ml',         'slug' => 'huile-sachet-500ml', 'module' => $mHuiles],

            // ─── Sucre & Farine ───
            ['nom' => 'Sucre sac 50kg',       'slug' => 'sucre-sac-50kg',     'module' => $mSucre],
            ['nom' => 'Sucre sachet 1kg',     'slug' => 'sucre-sachet-1kg',   'module' => $mSucre],
            ['nom' => 'Farine sac 50kg',      'slug' => 'farine-sac-50kg',    'module' => $mSucre],
            ['nom' => 'Farine sachet 1kg',    'slug' => 'farine-sachet-1kg',  'module' => $mSucre],

            // ─── Conserves & Pâtes ───
            ['nom' => 'Conserves poisson',    'slug' => 'conserves-poisson',  'module' => $mConserves],
            ['nom' => 'Conserves tomate',     'slug' => 'conserves-tomate',   'module' => $mConserves],
            ['nom' => 'Pâtes alimentaires',   'slug' => 'pates',              'module' => $mConserves],
            ['nom' => 'Sardines',             'slug' => 'sardines',           'module' => $mConserves],

            // ─── Légumes frais ───
            ['nom' => 'Oignons',              'slug' => 'oignons',            'module' => $mLegumes],
            ['nom' => 'Tomates',              'slug' => 'tomates',            'module' => $mLegumes],
            ['nom' => 'Ail',                  'slug' => 'ail',                'module' => $mLegumes],
            ['nom' => 'Pommes de terre',      'slug' => 'pommes-terre',       'module' => $mLegumes],

            // ─── Boissons ───
            ['nom' => 'Eau minérale',         'slug' => 'eau-minerale',       'module' => $mBoissons],
            ['nom' => 'Sucreries',            'slug' => 'sucreries',          'module' => $mBoissons],
            ['nom' => 'Jus',                  'slug' => 'jus',                'module' => $mBoissons],
            ['nom' => 'Boissons gazeuses',    'slug' => 'gazeuses',           'module' => $mBoissons],

            // ─── Produits laitiers ───
            ['nom' => 'Lait en poudre',       'slug' => 'lait-poudre',        'module' => $mLaitiers],
            ['nom' => 'Lait concentré',       'slug' => 'lait-concentre',     'module' => $mLaitiers],
            ['nom' => 'Yaourt',               'slug' => 'yaourt',             'module' => $mLaitiers],

            // ─── Hygiène & Entretien ───
            ['nom' => 'Savon',                'slug' => 'savon',              'module' => $mHygiene],
            ['nom' => 'Détergent',            'slug' => 'detergent',          'module' => $mHygiene],
            ['nom' => 'Papier hygiénique',    'slug' => 'papier-hygienique',  'module' => $mHygiene],

            // ─── Condiments ───
            ['nom' => 'Sel',                  'slug' => 'sel',                'module' => $mCondiments],
            ['nom' => 'Poivre & Épices',      'slug' => 'poivre-epices',      'module' => $mCondiments],
            ['nom' => 'Cube bouillon',        'slug' => 'cube-bouillon',      'module' => $mCondiments],

            // ─── Snacks ───
            ['nom' => 'Biscuits',             'slug' => 'biscuits',           'module' => $mSnacks],
            ['nom' => 'Chips & Cacahuètes',   'slug' => 'chips',              'module' => $mSnacks],
            ['nom' => 'Bonbons',              'slug' => 'bonbons',            'module' => $mSnacks],
        ];

        foreach ($categories as $cat) {
            Categorie::updateOrCreate(['slug' => $cat['slug']], [
                'nom'             => $cat['nom'],
                'module_stock_id' => $cat['module']->id,
                'is_active'       => true,
            ]);
        }
    }
}