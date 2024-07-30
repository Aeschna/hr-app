<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            AdminSeeder::class,
            UserSeeder::class,
            CompanySeeder::class, // Şirket Seeder'ı ekleyin
            EmployeeSeeder::class, // Çalışan Seeder'ı ekleyin
        ]);
    }
}

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->admin()->create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
        ]);
    }
}

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory(10)->create(); // Create 10 random employees
    }
}

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        \App\Models\Company::factory(20)->create(); // Create 20 random companies
    }
}

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        \App\Models\Employee::factory(100)->create(); // Create 100 random employees
    }
}
