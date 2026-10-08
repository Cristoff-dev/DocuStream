<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'Super Admin', 'slug' => 'super-admin', 'description' => 'System-wide access. Unrestricted.'],
            ['name' => 'Manager', 'slug' => 'manager', 'description' => 'Tenant-level administrative access.'],
            ['name' => 'Client', 'slug' => 'client', 'description' => 'Restricted read-only access to own tenant data.'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['slug' => $role['slug']], $role);
        }
    }
}