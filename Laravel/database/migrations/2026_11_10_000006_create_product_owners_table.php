<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Propriétaires des plantes / pépinières (Madagascar).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_owners', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 150);
            $table->string('pays', 100)->nullable()->default('Madagascar');
            $table->string('email', 150)->nullable();
            $table->text('infos_bancaires')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_owners');
    }
};
