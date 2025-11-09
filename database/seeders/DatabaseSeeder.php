<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            UserSeeder::class,
            QuestionPackageSeeder::class,
            QuestionSeeder::class,
            BatchSeeder::class,
            RegistrationCodeSeeder::class,
        ]);
    }
}
