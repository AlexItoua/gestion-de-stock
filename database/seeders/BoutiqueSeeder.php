<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Boutique;

class BoutiqueSeeder extends Seeder
{
    public function run(): void
    {
        // Zone 1 — Réception et stockage des cartons
        Boutique::firstOrCreate(['code' => 'DEP'], [
            'nom'         => 'Dépôt Poisson Salé',
            'adresse'     => 'Zone Industrielle, Ouenzé',
            'ville'       => 'Brazzaville',
            'telephone'   => '+242 06 987 65 43',
            'responsable' => 'Marie Ngoma',
            'type'        => 'depot',
        ]);

        // Zone 2 — Comptoir de vente (gros et détail)
        Boutique::firstOrCreate(['code' => 'CVT'], [
            'nom'         => 'Comptoir de Vente',
            'adresse'     => 'Avenue de la Paix, Centre-ville',
            'ville'       => 'Brazzaville',
            'telephone'   => '+242 06 123 45 67',
            'responsable' => 'Jean-Pierre Moukala',
            'type'        => 'boutique',
        ]);
    }
}
