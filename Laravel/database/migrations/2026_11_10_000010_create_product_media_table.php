<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Photos 1 à 4 (face, 2 côtés, arrière) + 1 vidéo ~15 s.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produit_id')->constrained('products')->cascadeOnDelete();
            $table->enum('type', ['photo', 'video']);
            $table->unsignedTinyInteger('position')->comment('Photos : 1-4 ; vidéo : 1');
            $table->enum('vue', ['front', 'side', 'back'])->nullable();
            $table->string('chemin');
            $table->unsignedSmallInteger('duree_secondes')->nullable();
            $table->boolean('est_vitrine')->default(false)->comment('Écran d accueil (max 20 photos)');
            $table->timestamps();

            $table->unique(['produit_id', 'type', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_media');
    }
};
