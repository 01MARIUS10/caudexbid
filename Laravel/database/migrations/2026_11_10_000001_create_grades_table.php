<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Référentiel des grades : general (G), regulier (M), premium (P), admin (A).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->char('code', 1)->unique()->comment('G / M / P / A');
            $table->string('nom', 30)->unique()->comment('general, regulier, premium, admin');
            $table->unsignedTinyInteger('niveau')->unique()->comment('0 = general, 3 = regulier, 5 = premium, 8 = admin');
            $table->boolean('peut_encherir')->default(false);
            $table->boolean('peut_changer_grade')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
