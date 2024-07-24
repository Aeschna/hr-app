<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;


class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if the admin user already exists
        if (!User::where('email', 'admin@admin.com')->exists()) {
            User::create([
                'name' => 'Admin',
                'email' => 'admin@admin.com',
                'password' => Hash::make('password'), // Change 'password' to a secure password
                'is_admin' => true, // Set is_admin to true
            ]);
        } else {
            // Update the existing admin user's is_admin field to true
            User::where('email', 'admin@admin.com')->update([
                'is_admin' => true,
            ]);
        }
    }
}
