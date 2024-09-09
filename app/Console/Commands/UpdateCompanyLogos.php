<?php

namespace App\Console\Commands;

use App\Models\Company;
use Illuminate\Console\Command;

class UpdateCompanyLogos extends Command
{
    protected $signature = 'update:company-logos';

    protected $description = 'Update the logo path for all companies';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info('Updating company logos...');

        // Update the logo path for all companies
        Company::query()->update(['logo' => 'storage/logos/company.png']);

        $this->info('Company logos updated successfully.');
    }
}
