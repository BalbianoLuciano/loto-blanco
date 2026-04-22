<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flashcard_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flashcard_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('rating');
            $table->float('stability_before');
            $table->float('stability_after');
            $table->float('difficulty_before');
            $table->float('difficulty_after');
            $table->unsignedInteger('time_spent_ms')->nullable();
            $table->timestamp('reviewed_at');
            $table->timestamps();

            $table->index(['flashcard_id', 'reviewed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flashcard_reviews');
    }
};
