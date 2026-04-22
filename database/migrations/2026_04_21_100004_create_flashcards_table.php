<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flashcards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('topic_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('document_chunk_id')->nullable()->constrained()->nullOnDelete();
            $table->text('front');
            $table->text('back');
            $table->string('source_type', 20)->default('manual');
            $table->float('stability')->default(0);
            $table->float('difficulty')->default(0.3);
            $table->unsignedInteger('reps')->default(0);
            $table->unsignedInteger('lapses')->default(0);
            $table->string('state', 20)->default('new');
            $table->timestamp('due_at')->nullable();
            $table->timestamp('last_review_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'state', 'due_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flashcards');
    }
};
