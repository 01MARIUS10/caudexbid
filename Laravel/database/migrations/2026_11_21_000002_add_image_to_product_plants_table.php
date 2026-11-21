<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Image d'une plante de la collection : identité visuelle dans le back office uniquement
 * (jamais utilisée pour les produits, qui ont leurs propres photos).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_plants', function (Blueprint $table) {
            $table->string('image')->nullable()->after('genre')->comment('Chemin sur le disque des médias');
        });
    }

    public function down(): void
    {
        Schema::table('product_plants', function (Blueprint $table) {
            $table->dropColumn('image');
        });
    }
};
