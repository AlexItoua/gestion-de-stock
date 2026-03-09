<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ventes', function (Blueprint $table) {
            $table->id();
            $table->string('numero_vente')->unique(); // Ex: "VTE-2024-000001"
            $table->foreignId('boutique_id')->constrained('boutiques')->onDelete('restrict');
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict'); // Vendeur

            $table->decimal('montant_total', 14, 2)->default(0);
            $table->decimal('montant_paye', 14, 2)->default(0);
            $table->decimal('monnaie_rendue', 14, 2)->default(0);

            $table->enum('statut', ['en_cours', 'finalisee', 'annulee'])->default('finalisee');
            $table->enum('mode_paiement', ['especes', 'mobile_money', 'cheque', 'credit', 'autre'])->default('especes');

            $table->string('nom_client')->nullable();
            $table->string('telephone_client')->nullable();

            $table->text('notes')->nullable();
            $table->timestamp('date_vente');
            $table->timestamps();
            $table->softDeletes();

            $table->index('boutique_id');
            $table->index('date_vente');
            $table->index('user_id');
            $table->index('statut');
        });

    }

    public function down(): void
    {

        Schema::dropIfExists('ventes');
    }
};
