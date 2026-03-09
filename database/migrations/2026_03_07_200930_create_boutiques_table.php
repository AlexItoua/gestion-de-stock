<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('boutiques', function (Blueprint $table) {
            $table->id();
            $table->string('nom');                        // Ex: "Boutique centrale", "Dépôt"
            $table->string('code')->unique();             // Ex: "BCT", "DEP"
            $table->string('adresse')->nullable();
            $table->string('ville')->nullable()->default('Brazzaville');
            $table->string('telephone')->nullable();
            $table->string('responsable')->nullable();
            $table->enum('type', ['boutique', 'depot', 'entrepot'])->default('boutique');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('boutiques');
    }
};
