<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Historique des grades. Grade actuel = ligne la plus récente du membre.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('user_auths')->cascadeOnDelete();
            $table->unsignedTinyInteger('grade_id');
            $table->foreign('grade_id')->references('id')->on('grades');
            $table->foreignId('edited_by_user_id')->nullable()->index()->constrained('user_auths')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['user_id', 'created_at'], 'idx_grade_actuel');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_grades');
    }
};
