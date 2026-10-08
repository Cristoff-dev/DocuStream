<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Report;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_cannot_download_report_from_another_tenant(): void
    {
        $managerRole = Role::firstOrCreate(
            ['slug' => 'manager'],
            ['name' => 'Manager']
        );

        $companyA = Company::factory()->create(['name' => 'Tech Corp A']);
        $companyB = Company::factory()->create(['name' => 'Global Corp B']);

        /** @var \App\Models\User $managerB */
        $managerB = User::factory()->create([
            'company_id' => $companyB->id,
            'role_id' => $managerRole->id,
        ]);
        $managerB->setRelation('role', $managerRole);

        $reportA = Report::factory()->create([
            'company_id' => $companyA->id,
            'name' => 'Financial_Audit_Company_A',
            'status' => 'completed',
            'file_path' => 'reports/secure_audit_a.pdf',
        ]);

        $url = URL::signedRoute('reports.download', ['report' => $reportA]);

        $response = $this->actingAs($managerB)->get($url);

        $this->assertContains($response->getStatusCode(), [403, 404]);
    }
    
    public function test_manager_can_download_own_tenant_report(): void
    {
        $managerRole = Role::firstOrCreate(
            ['slug' => 'manager'],
            ['name' => 'Manager']
        );

        $company = Company::factory()->create();
        
        /** @var \App\Models\User $manager */
        $manager = User::factory()->create([
            'company_id' => $company->id,
            'role_id' => $managerRole->id,
        ]);
        $manager->setRelation('role', $managerRole);

        $report = Report::factory()->create([
            'company_id' => $company->id,
            'status' => 'completed',
            'file_path' => 'reports/test_audit.pdf',
        ]);

        Storage::fake('s3');
        Storage::disk('s3')->put($report->file_path, 'dummy content');

        $url = URL::signedRoute('reports.download', ['report' => $report]);

        $response = $this->actingAs($manager)->get($url);

        $response->assertSuccessful();
    }
}