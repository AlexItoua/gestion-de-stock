<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Fournisseur;

class FournisseurSeeder extends Seeder
{
    public function run(): void
    {
        Fournisseur::firstOrCreate(['nom' => 'Importateurs Congo SARL'], [
            'contact_nom' => 'Alphonse Bissangou',
            'telephone'   => '+242 06 111 22 33',
            'email'       => 'contact@importcongo.cg',
            'adresse'     => 'Avenue Amilcar Cabral',
            'ville'       => 'Brazzaville',
            'pays'        => 'Congo',
        ]);

        Fournisseur::firstOrCreate(['nom' => 'Atlantic Fish Trading'], [
            'contact_nom' => 'Samuel Makosso',
            'telephone'   => '+242 05 444 55 66',
            'email'       => 'info@atlanticfish.cg',
            'adresse'     => 'Port Autonome de Pointe-Noire',
            'ville'       => 'Pointe-Noire',
            'pays'        => 'Congo',
        ]);
    }
}
