<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Table des modules (poisson, boulangerie, boissons, cave, etc.)
        Schema::create('modules_stock', function (Blueprint $table) {
            $table->id();
            $table->string('nom');                    // Ex: "poisson", "boulangerie"
            $table->string('slug')->unique();         // Ex: "poisson-sale", "boulangerie"
            $table->string('description')->nullable();
            $table->string('icone')->nullable();      // Nom d'icône UI
            $table->string('couleur', 7)->default('#3B82F6'); // Couleur hex pour l'UI
            $table->boolean('is_active')->default(true);
            $table->integer('ordre')->default(0);     // Ordre d'affichage
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modules_stock');
    }
};
