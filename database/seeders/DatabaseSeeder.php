<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\Employee;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class, // AdminSeeder'i UserSeeder ile birleştirdik
        ]);

        
        Company::factory(50)->create()->each(function ($company) {
            Employee::factory(4)->create(['company_id' => $company->id]);
        });
    }
}
