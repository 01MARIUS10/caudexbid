<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Table « Informations d'enchère » — supprimée avec le produit après règlement.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_bids', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produit_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('membre_id')->index()->constrained('user_auths');
            $table->decimal('montant', 12, 2);
            $table->decimal('pas_enchere', 12, 2);
            $table->enum('origine', ['manual', 'proxy'])->default('manual');
            $table->foreignId('enchere_auto_id')->nullable()->constrained('product_proxy_plafond_bids')->nullOnDelete();
            $table->boolean('a_prolonge')->default(false);
            $table->timestamp('created_at')->useCurrent();

            $table->index(['produit_id', 'montant']);
            $table->index(['produit_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_bids');
    }
};
