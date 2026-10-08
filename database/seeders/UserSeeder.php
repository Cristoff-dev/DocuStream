<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\Company;
use App\Models\ClientProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $superAdminRole = Role::where('slug', 'super-admin')->firstOrFail();
        $managerRole = Role::where('slug', 'manager')->firstOrFail();
        $clientRole = Role::where('slug', 'client')->firstOrFail();
        
        $company = Company::where('registration_number', 'US-123456789')->firstOrFail();

        User::firstOrCreate(
            ['email' => 'admin@docustream.test'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('password'),
                'role_id' => $superAdminRole->id,
                'company_id' => null,
                'permissions' => [],
            ]
        );

        User::firstOrCreate(
            ['email' => 'manager@acme.test'],
            [
                'name' => 'Acme Manager',
                'password' => Hash::make('password'),
                'role_id' => $managerRole->id,
                'company_id' => $company->id,
                'permissions' => ['edit_financials', 'request_heavy_audits'],
            ]
        );

        $clientUser = User::firstOrCreate(
            ['email' => 'client@acme.test'],
            [
                'name' => 'Acme Client',
                'password' => Hash::make('password'),
                'role_id' => $clientRole->id,
                'company_id' => $company->id,
                'permissions' => [],
            ]
        );

        ClientProfile::firstOrCreate(
            ['user_id' => $clientUser->id],
            [
                'id' => (string) Str::uuid(),
                'legal_name' => 'Acme Corporation C.A.',
                'contact_email' => 'client@acme.test',
                'tax_id' => 'J-98765432-1',
                'contact_phone' => '+15550199',
                'country_code' => 'US',
            ]
        );
    }
}