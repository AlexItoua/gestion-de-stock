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
        $boutique = Boutique::where('code', 'GAREL')->first();
        $depot    = Boutique::where('code', 'DEP')->first();

        // Admin
        $admin = User::firstOrCreate(['email' => 'admin@garelshop.com'], [
            'name'        => 'Administrateur',
            'password'    => Hash::make('admin@2024'),
            'phone'       => '+242 873 11 72',
            'boutique_id' => $depot->id,
            'is_active'   => true,
        ]);
        $admin->assignRole('admin');

        // Gestionnaire
        $gestionnaire = User::firstOrCreate(['email' => 'gestionnaire@garelshop.com'], [
            'name'        => 'Marie Ngoma',
            'password'    => Hash::make('Gest@2024'),
            'phone'       => '+242 06 000 00 02',
            'boutique_id' => $depot->id,
            'is_active'   => true,
        ]);
        $gestionnaire->assignRole('gestionnaire');

        // Vendeur
        $vendeur = User::firstOrCreate(['email' => 'vendeur@garelshop.com'], [
            'name'        => 'Pierre Loemba',
            'password'    => Hash::make('Vend@2024'),
            'phone'       => '+242 06 000 00 03',
            'boutique_id' => $boutique->id,
            'is_active'   => true,
        ]);
        $vendeur->assignRole('vendeur');
    }
}