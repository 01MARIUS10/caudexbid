<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expediteur_id')->constrained('user_auths');
            $table->foreignId('destinataire_id')->constrained('user_auths');
            $table->enum('type', ['general', 'regular_member_request', 'registration'])->default('general');
            $table->text('memo');
            $table->timestamp('envoye_le')->useCurrent();
            $table->timestamp('lu_le')->nullable();

            $table->index(['destinataire_id', 'envoye_le'], 'idx_boite_reception');
            $table->index(['expediteur_id', 'envoye_le'], 'idx_boite_envoi');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
