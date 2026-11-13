<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Session d'enchères mensuelle (mêmes dates pour tous les lots).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_auction_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('titre', 100)->nullable();
            $table->timestamp('debut_le');
            $table->timestamp('cloture_le')->comment('Clôture prévue (dimanche 20h-22h KST)');
            $table->enum('statut', ['scheduled', 'open', 'closed', 'settled'])->default('scheduled');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_auction_sessions');
    }
};
