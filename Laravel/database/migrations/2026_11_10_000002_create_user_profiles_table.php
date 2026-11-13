<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('membre_id')->unique()->constrained('user_auths')->cascadeOnDelete();
            $table->string('nom', 100)->nullable();
            $table->string('telephone', 30)->nullable();
            $table->text('adresse')->nullable()->comment('Adresse de livraison');
            $table->string('pays', 100)->nullable();
            $table->enum('nationalite', ['china', 'japan', 'korea', 'madagascar', 'other'])->default('other');
            $table->foreignId('parrain_id')->nullable()->index()->constrained('user_auths')->nullOnDelete()->comment('Parrain = membre premium');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
