<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('package_questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('package_id');
            $table->unsignedBigInteger('question_id');
            $table->integer('order_number');
            $table->timestamps();
            
            $table->foreign('package_id')->references('id')->on('question_packages')->onDelete('cascade');
            $table->foreign('question_id')->references('id')->on('questions')->onDelete('cascade');
            $table->unique(['package_id', 'question_id']);
            $table->unique(['package_id', 'order_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('package_questions');
    }
};
