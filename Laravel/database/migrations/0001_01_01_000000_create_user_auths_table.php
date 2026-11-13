<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Table racine des membres : identité + credentials (modèle Authenticatable).
 * Toutes les FK « membre » du schéma pointent sur user_auths.id.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_auths', function (Blueprint $table) {
            $table->id();
            $table->string('identifiant', 50)->unique()->comment('Identifiant de connexion (ex : andy88)');
            $table->string('email', 150)->unique()->comment('Connexion / réinitialisation du mot de passe');
            $table->string('password');
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamp('derniere_connexion_le')->nullable()->comment('Date de dernière connexion');
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('user_auths');
    }
};
