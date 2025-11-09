<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Batch;
use App\Models\QuestionPackage;
use Carbon\Carbon;

class BatchSeeder extends Seeder
{
    public function run()
    {
        $package = QuestionPackage::first();

        Batch::create([
            'name' => 'TOEFL Batch 1 - Morning',
            'description' => 'Morning session for TOEFL practice test',
            'question_package_id' => $package->id,
            'start_time' => Carbon::now()->addHours(1),
            'end_time' => Carbon::now()->addHours(4),
            'is_active' => true,
        ]);

        Batch::create([
            'name' => 'TOEFL Batch 2 - Afternoon',
            'description' => 'Afternoon session for TOEFL practice test',
            'question_package_id' => $package->id,
            'start_time' => Carbon::now()->addHours(5),
            'end_time' => Carbon::now()->addHours(8),
            'is_active' => true,
        ]);
    }
}
