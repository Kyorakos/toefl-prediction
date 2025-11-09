<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('question_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('listening_questions')->default(15);
            $table->integer('structure_questions')->default(30);
            $table->integer('reading_questions')->default(30);
            $table->integer('listening_time')->default(30); // minutes
            $table->integer('structure_time')->default(30); // minutes
            $table->integer('reading_time')->default(50); // minutes
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('question_packages');
    }
};