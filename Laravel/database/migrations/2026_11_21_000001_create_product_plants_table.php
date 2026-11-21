<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Collection des plantes : information générale d'une plante (nom, genre ; pas de propriétaire, de prix, de taille ni de description).
 *
 * Rôle : faciliter la saisie d'un produit (nom pré-rempli dans le formulaire, modifiable), le classement
 * et les statistiques. Un produit peut être lié à une plante (products.plante_id, facultatif) ; ses propres
 * colonnes (nom, descriptions, tailles, propriétaire, prix) restent la référence.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_plants', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 150)->unique();
            $table->string('genre', 100)->nullable()->index();
            $table->timestamps();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('plante_id')->nullable()->after('proprietaire_id')->constrained('product_plants')->nullOnDelete();
        });

        // Reprise : une plante par nom de produit distinct.
        DB::table('products')->orderBy('id')->get()->groupBy('nom')->each(function ($products, string $nom) {
            $planteId = DB::table('product_plants')->insertGetId([
                'nom' => $nom,
                'genre' => strtok($nom, ' ') ?: null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            DB::table('products')->whereIn('id', $products->pluck('id'))->update(['plante_id' => $planteId]);
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropConstrainedForeignId('plante_id');
        });
        Schema::dropIfExists('product_plants');
    }
};
