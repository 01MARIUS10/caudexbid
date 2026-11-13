<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Table « Produit » — supprimée après règlement.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_enchere_id')->index()->constrained('product_auction_sessions');
            $table->foreignId('proprietaire_id')->constrained('product_owners');
            $table->string('categorie', 50)->default('plant');
            $table->string('numero_lot', 20)->unique();
            $table->string('nom', 150);
            $table->text('description_1')->nullable();
            $table->text('description_2')->nullable();
            $table->text('description_3')->nullable();
            $table->decimal('taille_diametre', 8, 2)->nullable();
            $table->decimal('taille_circonference', 8, 2)->nullable();
            $table->decimal('taille_hauteur', 8, 2)->nullable();
            $table->decimal('prix_vente_existant', 12, 2)->nullable();
            $table->decimal('prix_depart', 12, 2);
            $table->decimal('pas_enchere', 12, 2);
            $table->timestamp('debut_enchere_le');
            $table->timestamp('cloture_prevue_le');
            $table->timestamp('cloture_effective_le')->index()->comment('Clôture réelle après soft close (max +30 min)');
            $table->unsignedTinyInteger('nb_prolongations')->default(0);
            $table->decimal('prix_actuel', 12, 2)->nullable();
            $table->foreignId('auteur_prix_actuel_id')->nullable()->constrained('user_auths')->nullOnDelete();
            $table->unsignedInteger('nb_encherisseurs')->default(0);
            $table->enum('statut', ['draft', 'active', 'sold', 'awarded', 'unsold'])->default('draft')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
