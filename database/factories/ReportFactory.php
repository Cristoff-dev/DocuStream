<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Company;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Report>
 */
class ReportFactory extends Factory
{
    protected $model = Report::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'user_id' => User::factory(),
            'name' => 'Financial_Audit_' . fake()->uuid(),
            'status' => 'pending',
            'file_path' => null,
            'processed_rows' => 0,
        ];
    }
}