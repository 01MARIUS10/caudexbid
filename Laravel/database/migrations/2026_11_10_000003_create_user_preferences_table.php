<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('membre_id')->unique()->constrained('user_auths')->cascadeOnDelete();
            $table->string('fuseau_horaire', 50)->default('Asia/Seoul');
            $table->boolean('notif_surenchere')->default(true);
            $table->boolean('notif_adjudication')->default(true);
            $table->boolean('notif_nouveau_message')->default(true);
            $table->boolean('notif_prochaine_session')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_preferences');
    }
};
