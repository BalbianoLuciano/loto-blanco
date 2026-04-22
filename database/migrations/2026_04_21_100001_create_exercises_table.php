<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exercises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('topic_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('document_id')->nullable()->constrained()->nullOnDelete();
            $table->string('source_type', 20)->default('extracted');
            $table->tinyInteger('difficulty')->default(3);
            $table->text('statement');
            $table->text('solution')->nullable();
            $table->text('hint')->nullable();
            $table->string('verified_by', 20)->nullable();
            $table->timestamps();

            $table->index(['subject_id', 'topic_id', 'difficulty']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exercises');
    }
};
