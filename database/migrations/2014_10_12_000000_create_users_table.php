<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password'); // Plaintext as requested
            $table->string('registration_code')->unique();
            $table->enum('role', ['admin', 'student'])->default('student');
            $table->enum('status', ['active', 'inactive', 'exam_in_progress', 'exam_completed'])->default('active');
            $table->integer('tab_switch_count')->default(0);
            $table->timestamp('last_activity')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
