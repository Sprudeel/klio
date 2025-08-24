<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');
        $name = env('ADMIN_NAME', 'Admin');

        if (!$email || !$password) {
            $this->command->warn('ADMIN_EMAIL or ADMIN_PASSWORD not set in .env — skipping AdminUserSeeder.');
            return;
        }

        if (!is_string($password) || trim($password) === '') {
            $this->command?->warn('ADMIN_PASSWORD is empty or invalid — skipping AdminUserSeeder.');
            return;
        }

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name'     => $name,
                'password' => Hash::make((string) $password),
                'is_admin' => true,
            ]
        );

        $this->command->info("Admin user ready: {$user->email}");
    }
}
