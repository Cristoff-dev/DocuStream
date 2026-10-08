<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Report;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class GenerateHeavyReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300;
    public int $tries = 3;

    public readonly string $reportId;
    public readonly string $timezone;

    public function __construct(
        string $reportId,
        ?string $timezone = null
    ) {
        $this->reportId = $reportId;
        $this->timezone = $timezone ?: 'UTC';
    }

    public function handle(): void
    {
        $report = Report::find($this->reportId);

        if (!$report) {
            Log::warning("GenerateHeavyReportJob aborted: Report {$this->reportId} not found.");
            return;
        }

        $report->update(['status' => 'processing']);

        try {
            $data = $this->generateDummyData(50, $report->company_id); 

            $pdf = Pdf::loadView('reports.pdf.financial_template', [
                'data' => $data,
                'timezone' => $this->timezone
            ]);
            
            $fileName = sprintf('reports/company_%s/report_%s.pdf', $report->company_id, $report->id);

            Storage::disk('s3')->put($fileName, $pdf->output());

            $report->update([
                'status' => 'completed',
                'file_path' => $fileName,
                'processed_rows' => count($data),
            ]);

            Log::info("Report {$this->reportId} generated and uploaded securely.");

        } catch (Throwable $exception) {
            $report->update(['status' => 'failed']);
            
            Log::error("Could not generate report {$this->reportId}.", [
                'error' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);

            throw $exception;
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function generateDummyData(int $rows, string $companyId): array
    {
        $data = [];
        for ($i = 0; $i < $rows; $i++) {
            $data[] = [
                'transaction_id' => sprintf('TRX-%s-%06d', substr($companyId, 0, 8), $i),
                'amount' => rand(100, 10000) / 100,
                'date' => now()->subDays(rand(1, 365))->format('Y-m-d'),
                'status' => 'Cleared'
            ];
        }
        return $data;
    }
}