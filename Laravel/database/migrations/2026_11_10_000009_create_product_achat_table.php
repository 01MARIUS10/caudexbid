<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Achat immédiat (1-1 avec products). Ligne présente seulement si le lot propose un achat immédiat.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_achat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produit_id')->unique()->constrained('products')->cascadeOnDelete();
            $table->decimal('prix', 12, 2);
            $table->foreignId('acheteur_id')->nullable()->constrained('user_auths')->nullOnDelete();
            $table->timestamp('achete_le')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_achat');
    }
};
