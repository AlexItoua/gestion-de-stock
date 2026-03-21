<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Boutique;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $comptoir = Boutique::where('code', 'CVT')->first(); // Comptoir de vente
        $depot    = Boutique::where('code', 'DEP')->first(); // Dépôt principal

        // Admin — rattaché au dépôt (gère tout)
        $admin = User::firstOrCreate(['email' => 'admin@gestionstock.com'], [
            'name'        => 'Administrateur',
            'password'    => Hash::make('Admin@2024!'),
            'phone'       => '+242 06 000 00 01',
            'boutique_id' => $depot->id,
            'is_active'   => true,
        ]);
        $admin->assignRole('admin');

        // Gestionnaire — rattaché au dépôt
        $gestionnaire = User::firstOrCreate(['email' => 'gestionnaire@gestionstock.com'], [
            'name'        => 'Marie Ngoma',
            'password'    => Hash::make('Gest@2024!'),
            'phone'       => '+242 06 000 00 02',
            'boutique_id' => $depot->id,
            'is_active'   => true,
        ]);
        $gestionnaire->assignRole('gestionnaire');

        // Vendeur — rattaché au comptoir de vente
        $vendeur = User::firstOrCreate(['email' => 'vendeur@gestionstock.com'], [
            'name'        => 'Pierre Loemba',
            'password'    => Hash::make('Vend@2024!'),
            'phone'       => '+242 06 000 00 03',
            'boutique_id' => $comptoir->id,
            'is_active'   => true,
        ]);
        $vendeur->assignRole('vendeur');
    }
}
