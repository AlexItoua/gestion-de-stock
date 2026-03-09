<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Boutique;

class BoutiqueSeeder extends Seeder
{
    public function run(): void
    {
        Boutique::firstOrCreate(['code' => 'BCT'], [
            'nom'         => 'Boutique Centrale',
            'adresse'     => 'Avenue de la Paix, Centre-ville',
            'ville'       => 'Brazzaville',
            'telephone'   => '+242 06 123 45 67',
            'responsable' => 'Jean-Pierre Moukala',
            'type'        => 'boutique',
        ]);

        Boutique::firstOrCreate(['code' => 'DEP'], [
            'nom'         => 'Dépôt Principal',
            'adresse'     => 'Zone Industrielle, Ouenzé',
            'ville'       => 'Brazzaville',
            'telephone'   => '+242 06 987 65 43',
            'responsable' => 'Marie Ngoma',
            'type'        => 'depot',
        ]);
    }
}
