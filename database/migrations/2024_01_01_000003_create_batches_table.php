<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('question_package_id');
            $table->datetime('start_time');
            $table->datetime('end_time');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->foreign('question_package_id')->references('id')->on('question_packages')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('batches');
    }
};
