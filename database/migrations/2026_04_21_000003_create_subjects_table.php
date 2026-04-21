<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('element', 10)->default('lotus');
            $table->string('color', 7)->default('#1a1a1a');
            $table->text('description')->nullable();
            $table->boolean('archived')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'archived']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
