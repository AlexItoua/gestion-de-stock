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
        $boutique = Boutique::where('code', 'BCT')->first();

        $admin = User::firstOrCreate(['email' => 'admin@gestionstock.com'], [
            'name'        => 'Administrateur',
            'password'    => Hash::make('Admin@2024!'),
            'phone'       => '+242 06 000 00 01',
            'boutique_id' => $boutique->id,
            'is_active'   => true,
        ]);
        $admin->assignRole('admin'); // fonctionne maintenant car guard = 'web'

        $gestionnaire = User::firstOrCreate(['email' => 'gestionnaire@gestionstock.com'], [
            'name'        => 'Marie Ngoma',
            'password'    => Hash::make('Gest@2024!'),
            'phone'       => '+242 06 000 00 02',
            'boutique_id' => $boutique->id,
            'is_active'   => true,
        ]);
        $gestionnaire->assignRole('gestionnaire');

        $vendeur = User::firstOrCreate(['email' => 'vendeur@gestionstock.com'], [
            'name'        => 'Pierre Loemba',
            'password'    => Hash::make('Vend@2024!'),
            'phone'       => '+242 06 000 00 03',
            'boutique_id' => $boutique->id,
            'is_active'   => true,
        ]);
        $vendeur->assignRole('vendeur');
    }
}
