<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Permissions
        $permissions = [
            'produits.voir', 'produits.creer', 'produits.modifier', 'produits.supprimer',
            'stock.voir', 'stock.entree', 'stock.sortie', 'stock.ajustement', 'stock.transfert',
            'ventes.voir', 'ventes.creer', 'ventes.annuler',
            'rapports.voir',
            'fournisseurs.gerer',
            'categories.gerer',
            'boutiques.gerer',
            'users.gerer',
            'modules.gerer',
            'alertes.voir', 'alertes.resoudre',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web', // <- changé de 'sanctum' à 'web'
            ]);
        }

        // Roles
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions(Permission::all());

        $gestionnaire = Role::firstOrCreate(['name' => 'gestionnaire', 'guard_name' => 'web']);
        $gestionnaire->syncPermissions([
            'produits.voir', 'produits.creer', 'produits.modifier',
            'stock.voir', 'stock.entree', 'stock.sortie', 'stock.ajustement', 'stock.transfert',
            'ventes.voir', 'ventes.creer', 'ventes.annuler',
            'rapports.voir',
            'fournisseurs.gerer', 'categories.gerer',
            'alertes.voir', 'alertes.resoudre',
        ]);

        $vendeur = Role::firstOrCreate(['name' => 'vendeur', 'guard_name' => 'web']);
        $vendeur->syncPermissions([
            'produits.voir',
            'stock.voir',
            'ventes.voir', 'ventes.creer',
            'alertes.voir',
        ]);
    }
}
