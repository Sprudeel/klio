<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teacher1 = User::firstOrCreate(
            ['email' => 'teacher1@example.com'],
            [
                'name' => 'Teacher One',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $teacher2 = User::firstOrCreate(
            ['email' => 'teacher2@example.com'],
            [
                'name' => 'Teacher Two',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
    }
}
