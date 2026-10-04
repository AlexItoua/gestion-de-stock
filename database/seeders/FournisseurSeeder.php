<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Fournisseur;

class FournisseurSeeder extends Seeder
{
    public function run(): void
    {
        $fournisseurs = [
            [
                'nom'         => 'Grossiste Riz Congo',
                'contact_nom' => 'Alphonse Bissangou',
                'telephone'   => '+242 06 111 22 33',
                'email'       => 'contact@grossisteriz.cg',
                'adresse'     => 'Marché Total, Brazzaville',
                'ville'       => 'Brazzaville',
                'pays'        => 'Congo',
            ],
            [
                'nom'         => 'Import Afrique SARL',
                'contact_nom' => 'Samuel Makosso',
                'telephone'   => '+242 05 444 55 66',
                'email'       => 'info@importafrique.cg',
                'adresse'     => 'Zone Industrielle',
                'ville'       => 'Pointe-Noire',
                'pays'        => 'Congo',
            ],
            [
                'nom'         => 'Distrib Huiles & Savons',
                'contact_nom' => 'Grace Loubaki',
                'telephone'   => '+242 06 555 66 77',
                'email'       => 'contact@distribhuiles.cg',
                'adresse'     => 'Avenue de la Paix',
                'ville'       => 'Brazzaville',
                'pays'        => 'Congo',
            ],
            [
                'nom'         => 'Maraîcher Central',
                'contact_nom' => 'Pierre Ngoma',
                'telephone'   => '+242 06 888 99 00',
                'email'       => 'contact@maraicher.cg',
                'adresse'     => 'Marché Central',
                'ville'       => 'Brazzaville',
                'pays'        => 'Congo',
            ],
            [
                'nom'         => 'Conserves & Pâtes Import',
                'contact_nom' => 'Marie Ngoyi',
                'telephone'   => '+242 05 777 88 99',
                'email'       => 'contact@conserves.cg',
                'adresse'     => 'Port Autonome',
                'ville'       => 'Pointe-Noire',
                'pays'        => 'Congo',
            ],
            [
                'nom'         => 'Boissons & Rafraîchissements',
                'contact_nom' => 'Jean-Pierre Moukala',
                'telephone'   => '+242 06 123 45 67',
                'email'       => 'contact@boissons.cg',
                'adresse'     => 'Avenue de la Paix',
                'ville'       => 'Brazzaville',
                'pays'        => 'Congo',
            ],
        ];

        foreach ($fournisseurs as $f) {
            Fournisseur::updateOrCreate(['nom' => $f['nom']], $f);
        }
    }
}