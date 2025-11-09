<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('batch_id');
            $table->unsignedBigInteger('question_package_id');
            $table->enum('current_section', ['listening', 'structure', 'reading'])->default('listening');
            $table->integer('current_question')->default(1);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('listening_started_at')->nullable();
            $table->timestamp('structure_started_at')->nullable();
            $table->timestamp('reading_started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->enum('status', ['not_started', 'in_progress', 'completed', 'submitted'])->default('not_started');
            $table->integer('total_score')->nullable();
            $table->integer('listening_score')->nullable();
            $table->integer('structure_score')->nullable();
            $table->integer('reading_score')->nullable();
            $table->integer('tab_switch_count')->default(0);
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('batch_id')->references('id')->on('batches')->onDelete('cascade');
            $table->foreign('question_package_id')->references('id')->on('question_packages')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_sessions');
    }
};
