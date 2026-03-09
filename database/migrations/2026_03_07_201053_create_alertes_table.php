<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alertes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produit_id')->nullable()->constrained('produits')->onDelete('cascade');
            $table->foreignId('boutique_id')->nullable()->constrained('boutiques')->onDelete('cascade');

            $table->enum('type_alerte', [
                'stock_faible',
                'stock_epuise',
                'expiration_proche',
                'produit_expire',
            ]);

            $table->string('titre');
            $table->text('message');
            $table->enum('niveau', ['info', 'warning', 'danger'])->default('warning');
            $table->boolean('is_lue')->default(false);
            $table->boolean('is_resolue')->default(false);

            $table->decimal('valeur_actuelle', 10, 3)->nullable(); // Stock actuel ou jours restants
            $table->decimal('valeur_seuil', 10, 3)->nullable();    // Seuil configuré

            $table->timestamp('date_resolution')->nullable();
            $table->timestamps();

            $table->index(['type_alerte', 'is_lue']);
            $table->index('produit_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alertes');
    }
};
