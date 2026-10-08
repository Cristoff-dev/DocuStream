<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\Report;

class ReportObserver
{
    public function created(Report $report): void
    {
        $action = $report->status === 'pending_review' ? 'Document Uploaded' : 'Audit Generated';
        
        $this->logActivity($report, $action, null, [
            'file_name' => $report->name,
            'status' => $report->status,
        ]);
    }

    public function updated(Report $report): void
    {
        if ($report->isDirty('status')) {
            $oldStatus = $report->getOriginal('status');
            $newStatus = $report->status;

            $action = 'Document ' . ucfirst($newStatus);

            $this->logActivity($report, $action, ['status' => $oldStatus], ['status' => $newStatus]);
        }
    }

    public function deleted(Report $report): void
    {
        $this->logActivity($report, 'Document Deleted', $report->toArray(), null);
    }

    protected function logActivity(Report $model, string $action, ?array $old, ?array $new): void
    {
        AuditLog::create([
            'company_id' => $model->company_id,
            'user_id'    => auth()->id() ?? $model->user_id,
            'action'     => $action,
            'model_type' => get_class($model),
            'model_id'   => (string) $model->id,
            'ip_address' => request()->ip(),
            'metadata'   => [
                'old'        => $old,
                'new'        => $new,
                'url'        => app()->runningInConsole() ? 'Background Job' : request()->fullUrl(),
                'user_agent' => request()->userAgent() ?? 'System / Queue Worker'
            ],
        ]);
    }
}