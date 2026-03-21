<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Alerte;
use App\Models\Produit;
use App\Models\Boutique;

class AlerteSeeder extends Seeder
{
    public function run(): void
    {
        $comptoir = Boutique::where('code', 'CVT')->first(); // Comptoir de vente
        $depot    = Boutique::where('code', 'DEP')->first(); // Dépôt principal

        $p1 = Produit::where('code_produit', 'PSC-0001')->first();
        $p2 = Produit::where('code_produit', 'PSC-0002')->first();
        $p3 = Produit::where('code_produit', 'PSC-0003')->first();
        $p4 = Produit::where('code_produit', 'PSC-0004')->first();

        $alertes = [
            [
                'produit_id'  => $p1->id,
                'boutique_id' => $comptoir->id,
                'type_alerte' => 'stock_faible',
                'niveau'      => 'warning',
                'titre'       => 'Stock faible — Poisson Salé 10kg Grade A',
                'message'     => 'Le stock au comptoir est en dessous du seuil d\'alerte (45 cartons, seuil: 10).',
                'is_lue'      => false,
                'is_resolue'  => false,
            ],
            [
                'produit_id'  => $p3->id,
                'boutique_id' => $comptoir->id,
                'type_alerte' => 'stock_faible',
                'niveau'      => 'warning',
                'titre'       => 'Stock faible — Poisson Salé Carton 3kg',
                'message'     => 'Le stock au comptoir approche du seuil minimum (80 cartons, seuil: 20).',
                'is_lue'      => false,
                'is_resolue'  => false,
            ],
            [
                'produit_id'  => $p4->id,
                'boutique_id' => $comptoir->id,
                'type_alerte' => 'stock_epuise',
                'niveau'      => 'danger',
                'titre'       => 'Stock épuisé — Poisson Salé 10kg Grade B',
                'message'     => 'Produit complètement épuisé au Comptoir de Vente.',
                'is_lue'      => false,
                'is_resolue'  => false,
            ],
            [
                'produit_id'  => $p2->id,
                'boutique_id' => $depot->id,
                'type_alerte' => 'expiration_proche',
                'niveau'      => 'warning',
                'titre'       => 'Expiration proche — Poisson Salé Carton 5kg',
                'message'     => 'Produit expire dans moins de 30 jours au Dépôt.',
                'is_lue'      => false,
                'is_resolue'  => false,
            ],
            [
                'produit_id'  => $p1->id,
                'boutique_id' => $depot->id,
                'type_alerte' => 'stock_faible',
                'niveau'      => 'info',
                'titre'       => 'Vérification hebdomadaire effectuée',
                'message'     => 'Vérification stock hebdomadaire effectuée.',
                'is_lue'      => true,
                'is_resolue'  => false,
            ],
        ];

        foreach ($alertes as $data) {
            Alerte::firstOrCreate(
                [
                    'produit_id'  => $data['produit_id'],
                    'boutique_id' => $data['boutique_id'],
                    'type_alerte' => $data['type_alerte'],
                    'is_resolue'  => false,
                ],
                $data
            );
        }
    }
}
