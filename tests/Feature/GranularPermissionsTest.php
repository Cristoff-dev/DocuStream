<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Report;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GranularPermissionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_cannot_delete_report_without_granular_permission(): void
    {
        $managerRole = Role::firstOrCreate(['slug' => 'manager'], ['name' => 'Manager']);
        $company = Company::factory()->create();
        
        /** @var \App\Models\User $manager */
        $manager = User::factory()->create([
            'company_id' => $company->id,
            'role_id' => $managerRole->id,
            'permissions' => ['request_heavy_audits'],
        ]);
        $manager->setRelation('role', $managerRole);

        $report = Report::factory()->create([
            'company_id' => $company->id,
            'status' => 'completed',
        ]);

        $response = $this->actingAs($manager)->delete(route('reports.destroy', $report));

        $response->assertForbidden();
        $this->assertDatabaseHas('reports', ['id' => $report->id]);
    }

    public function test_manager_can_delete_report_with_granular_permission(): void
    {
        $managerRole = Role::firstOrCreate(['slug' => 'manager'], ['name' => 'Manager']);
        $company = Company::factory()->create();
        
        /** @var \App\Models\User $manager */
        $manager = User::factory()->create([
            'company_id' => $company->id,
            'role_id' => $managerRole->id,
            'permissions' => ['delete_reports'],
        ]);
        $manager->setRelation('role', $managerRole);

        $report = Report::factory()->create([
            'company_id' => $company->id,
            'status' => 'completed',
        ]);

        $response = $this->actingAs($manager)->delete(route('reports.destroy', $report));

        $response->assertRedirect(); 
        $this->assertSoftDeleted('reports', ['id' => $report->id]);
    }
}