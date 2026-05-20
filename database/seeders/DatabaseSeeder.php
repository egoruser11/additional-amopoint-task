<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $password = env('ADMIN_PASSWORD');

        if (! is_string($password) || $password === '') {
            throw new RuntimeException('ADMIN_PASSWORD must be set before seeding.');
        }

        User::query()->updateOrCreate([
            'email' => env('ADMIN_EMAIL', 'admin@example.com'),
        ], [
            'name' => env('ADMIN_NAME', 'Admin'),
            'password' => Hash::make($password),
        ]);

        $this->call(PageVisitSeeder::class);
    }
}
