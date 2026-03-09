<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produits', function (Blueprint $table) {
            $table->id();
            $table->string('code_produit')->unique();    // Ex: "PSC-10KG-001"
            $table->string('nom');                       // Ex: "Poisson salé carton 10kg"
            $table->foreignId('module_stock_id')->constrained('modules_stock')->onDelete('restrict');
            $table->foreignId('categorie_id')->constrained('categories')->onDelete('restrict');
            $table->foreignId('fournisseur_id')->nullable()->constrained('fournisseurs')->onDelete('set null');

            // Prix en Francs CFA
            $table->decimal('prix_achat', 12, 2)->default(0);        // Prix d'achat du carton
            $table->decimal('prix_vente_gros', 12, 2)->default(0);   // Prix vente carton entier
            $table->decimal('prix_vente_detail', 12, 2)->default(0); // Prix vente au détail (par kg, pièce)

            // Unités
            $table->enum('unite_stock', ['carton', 'kg', 'litre', 'piece', 'sac', 'bouteille'])->default('carton');
            $table->enum('unite_detail', ['kg', 'piece', 'litre', 'portion'])->nullable();

            // Pour poisson: contenance du carton (ex: 10 pour 10kg)
            $table->decimal('contenance_carton', 8, 3)->nullable(); // 10, 5, 3 (kg par carton)

            // Seuils
            $table->integer('seuil_alerte')->default(10);   // Alerte stock faible
            $table->integer('stock_minimum')->default(5);    // Stock minimum absolu

            // Expiration
            $table->date('date_expiration')->nullable();
            $table->integer('jours_alerte_expiration')->default(30); // Alerter X jours avant

            $table->string('description')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('vente_detail_possible')->default(true); // Peut-on vendre au détail?

            $table->timestamps();
            $table->softDeletes();

            $table->index(['module_stock_id', 'categorie_id']);
            $table->index('code_produit');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produits');
    }
};
