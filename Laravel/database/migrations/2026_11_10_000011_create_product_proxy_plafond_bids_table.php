<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Plafond confidentiel des enchères automatiques : un par membre et par lot.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_proxy_plafond_bids', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produit_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('membre_id')->constrained('user_auths');
            $table->decimal('plafond', 12, 2);
            $table->boolean('est_actif')->default(true);
            $table->timestamps();

            $table->unique(['produit_id', 'membre_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_proxy_plafond_bids');
    }
};
