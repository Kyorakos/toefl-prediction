<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RegistrationCode;
use App\Models\Batch;
use Illuminate\Support\Str;

class RegistrationCodeSeeder extends Seeder
{
    public function run()
    {
        $batches = Batch::all();

        foreach ($batches as $batch) {
            // Generate 10 registration codes per batch
            for ($i = 1; $i <= 10; $i++) {
                RegistrationCode::create([
                    'code' => 'REG' . strtoupper(Str::random(8)),
                    'batch_id' => $batch->id,
                    'is_used' => false,
                ]);
            }
        }

        // Mark demo registration code as used
        RegistrationCode::create([
            'code' => 'DEMO001',
            'batch_id' => $batches->first()->id,
            'is_used' => true,
            'used_by' => 2, // Student Demo user
            'used_at' => now(),
        ]);
    }
}
