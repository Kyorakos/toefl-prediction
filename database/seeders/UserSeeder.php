<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Create admin user
        User::create([
            'name' => 'Admin TOEFL',
            'email' => 'admin@toefl.com',
            'password' => 'admin123',
            'role' => 'admin',
            'registration_code' => 'ADMIN001',
        ]);

        // Create sample student
        User::create([
            'name' => 'Student Demo',
            'email' => 'student@toefl.com',
            'password' => 'student123',
            'role' => 'student',
            'registration_code' => 'DEMO001',
        ]);
    }
}
