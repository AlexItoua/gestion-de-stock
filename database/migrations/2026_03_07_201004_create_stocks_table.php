<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Stock par produit ET par boutique
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produit_id')->constrained('produits')->onDelete('restrict');
            $table->foreignId('boutique_id')->constrained('boutiques')->onDelete('restrict');
            $table->decimal('quantite', 10, 3)->default(0);          // Quantité en stock (cartons)
            $table->decimal('quantite_detail', 10, 3)->default(0);   // Reste en détail (kg ouverts)
            $table->decimal('valeur_stock', 14, 2)->default(0);      // Valeur calculée
            $table->timestamp('derniere_mise_a_jour')->nullable();
            $table->timestamps();

            // Un produit = un stock par boutique (unique)
            $table->unique(['produit_id', 'boutique_id']);
            $table->index('boutique_id');
            $table->index('produit_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
