<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subject_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('element', 10)->default('lotus');
            $table->string('color', 7)->default('#1a1a1a');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subject_templates');
    }
};
