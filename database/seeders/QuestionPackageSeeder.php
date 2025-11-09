<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\QuestionPackage;

class QuestionPackageSeeder extends Seeder
{
    public function run()
    {
        QuestionPackage::create([
            'name' => 'TOEFL Package 1',
            'description' => 'Standard TOEFL Practice Test Package',
            'listening_questions' => 15,
            'structure_questions' => 30,
            'reading_questions' => 30,
            'listening_time' => 30,
            'structure_time' => 30,
            'reading_time' => 50,
            'is_active' => true,
        ]);

        QuestionPackage::create([
            'name' => 'TOEFL Package 2',
            'description' => 'Advanced TOEFL Practice Test Package',
            'listening_questions' => 15,
            'structure_questions' => 30,
            'reading_questions' => 30,
            'listening_time' => 30,
            'structure_time' => 30,
            'reading_time' => 50,
            'is_active' => true,
        ]);
    }
}
