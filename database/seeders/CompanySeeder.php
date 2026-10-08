<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        Company::firstOrCreate(
            ['registration_number' => 'US-123456789'],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Acme Corporation',
                'is_active' => true,
            ]
        );
    }
}