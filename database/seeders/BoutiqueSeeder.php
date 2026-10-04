<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Boutique;

class BoutiqueSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Dépôt principal (stockage) ───
        Boutique::updateOrCreate(['code' => 'DEP'], [
            'nom'         => 'Dépôt Garel Shop',
            'adresse'     => 'Zone Industrielle, Ouenzé',
            'ville'       => 'Brazzaville',
            'telephone'   => '+242 06 987 65 43',
            'responsable' => 'Gérant Garel Shop',
            'type'        => 'depot',
            'is_active'   => true,
        ]);

        // ─── Boutique principale ───
        Boutique::updateOrCreate(['code' => 'GAREL'], [
            'nom'         => 'Garel Shop',
            'adresse'     => 'Avenue de la Paix, Centre-ville',
            'ville'       => 'Brazzaville',
            'telephone'   => '+242 06 123 45 67',
            'responsable' => 'Gérant Garel Shop',
            'type'        => 'boutique',
            'is_active'   => true,
        ]);
    }
}