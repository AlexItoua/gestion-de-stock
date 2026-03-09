<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ventes_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vente_id')->constrained('ventes')->onDelete('cascade');
            $table->foreignId('produit_id')->constrained('produits')->onDelete('restrict');
            $table->enum('type_vente', ['gros', 'detail'])->default('gros');
            $table->decimal('quantite', 10, 3);
            $table->decimal('prix_unitaire', 15, 2);
            $table->decimal('sous_total', 15, 2);
            $table->decimal('prix_achat_snapshot', 15, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ventes_details');
    }
};
