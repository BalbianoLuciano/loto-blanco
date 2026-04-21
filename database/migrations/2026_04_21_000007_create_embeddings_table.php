<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('CREATE EXTENSION IF NOT EXISTS vector');

        Schema::create('embeddings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_chunk_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        // text-embedding-3-small outputs 1536 dimensions
        DB::statement('ALTER TABLE embeddings ADD COLUMN vector vector(1536)');

        // HNSW index for fast cosine similarity search
        DB::statement('CREATE INDEX embeddings_vector_idx ON embeddings USING hnsw (vector vector_cosine_ops)');
    }

    public function down(): void
    {
        Schema::dropIfExists('embeddings');
    }
};
