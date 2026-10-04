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

        // ─── ADMIN — rattaché à GAREL SHOP (le seul local au démarrage) ───
        $admin = User::firstOrCreate(['email' => 'admin@garelshop.com'], [
            'name'        => 'Administrateur',
            'password'    => Hash::make('admin@2024'),
            'phone'       => '068731172',
            'boutique_id' => $boutique->id,   // ← GAREL, pas DEP
            'is_active'   => true,
        ]);
        $admin->assignRole('admin');

        // ─── GESTIONNAIRE — rattaché à GAREL SHOP ───
        $gestionnaire = User::firstOrCreate(['email' => 'gestionnaire@garelshop.com'], [
            'name'        => 'Marie Ngoma',
            'password'    => Hash::make('Gest@2024'),
            'phone'       => '060000002',
            'boutique_id' => $boutique->id,   // ← GAREL
            'is_active'   => true,
        ]);
        $gestionnaire->assignRole('gestionnaire');

        // ─── VENDEUR — rattaché à GAREL SHOP ───
        $vendeur = User::firstOrCreate(['email' => 'vendeur@garelshop.com'], [
            'name'        => 'Pierre Loemba',
            'password'    => Hash::make('Vend@2024'),
            'phone'       => '060000003',
            'boutique_id' => $boutique->id,   // ← GAREL
            'is_active'   => true,
        ]);
        $vendeur->assignRole('vendeur');
    }
}