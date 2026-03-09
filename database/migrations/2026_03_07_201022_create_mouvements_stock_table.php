<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mouvements_stock', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique(); // Ex: "MVT-2024-000001"

            $table->foreignId('produit_id')->constrained('produits')->onDelete('restrict');
            $table->foreignId('boutique_id')->constrained('boutiques')->onDelete('restrict');
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');

            $table->enum('type_mouvement', [
                'entree',       // Réception stock fournisseur
                'vente',        // Vente client
                'perte',        // Perte / casse / avarie
                'ajustement',   // Correction inventaire
                'transfert_sortie', // Transfert vers autre boutique
                'transfert_entree', // Réception depuis autre boutique
                'retour_client',    // Retour de marchandise
            ]);

            $table->decimal('quantite', 10, 3);        // Quantité mouvementée
            $table->decimal('quantite_avant', 10, 3);  // Stock avant mouvement
            $table->decimal('quantite_apres', 10, 3);  // Stock après mouvement

            $table->decimal('prix_unitaire', 12, 2)->default(0); // Prix au moment du mvt
            $table->decimal('valeur_totale', 14, 2)->default(0);

            // Pour les transferts
            $table->foreignId('boutique_destination_id')->nullable()->constrained('boutiques')->onDelete('set null');
            $table->unsignedBigInteger('mouvement_lie_id')->nullable(); // Lien transfert sortie/entrée

            // Pour les ventes liées
            $table->unsignedBigInteger('vente_id')->nullable();

            $table->text('commentaire')->nullable();
            $table->timestamp('date_mouvement');

            $table->timestamps();

            $table->index(['produit_id', 'boutique_id']);
            $table->index('type_mouvement');
            $table->index('date_mouvement');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mouvements_stock');
    }
};
