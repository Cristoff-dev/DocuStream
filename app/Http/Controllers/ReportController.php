<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Jobs\GenerateHeavyReportJob;
use App\Models\Report;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Throwable;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Report::class);

        $reports = Report::where('company_id', $request->user()->company_id)
            ->latest()
            ->paginate(10);
        
        return view('reports.index', compact('reports'));
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', Report::class);

        $timezone = $request->input('client_timezone', 'UTC');

        $report = $this->provisionReport('Standard_Report_', $request->user()->company_id);
        GenerateHeavyReportJob::dispatch($report->id, $timezone);

        return back()->with('success', 'Standard report generation queued successfully.');
    }

    public function upload(Request $request): RedirectResponse
    {
        Gate::authorize('create', Report::class);

        $request->validate([
            'name' => 'required|string|max:255',
            'document' => 'required|file|mimes:pdf,xlsx,xls,doc,docx|max:20480',
            'client_id' => 'nullable|exists:users,id',
        ]);

        try {
            DB::beginTransaction();

            $file = $request->file('document');
            $companyId = $request->user()->company_id;
            
            $folderPath = sprintf('financial_audits/company_%s/%s', $companyId, now()->format('Y/m'));
            $path = $file->store($folderPath, 's3');

            $status = $request->user()->hasRole('manager') ? 'completed' : 'pending_review';

            $report = Report::create([
                'company_id' => $companyId,
                'name' => $request->input('name'),
                'status' => $status,
                'file_path' => $path,
                'processed_rows' => 0,
                'user_id' => $request->input('client_id') ?? ($request->user()->hasRole('client') ? $request->user()->id : null),
            ]);

            Log::info('Document uploaded manually', [
                'report_id' => $report->id,
                'company_id' => $companyId,
                'user_id' => $request->user()->id,
                'initial_status' => $status,
            ]);

            DB::commit();

            return back()->with('success', 'Document uploaded and securely stored.');
        } catch (Throwable $e) {
            DB::rollBack();

            Log::error('Manual document upload failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return back()->with('error', 'There was a problem processing the file.');
        }
    }

    public function approve(Report $report): RedirectResponse
    {
        Gate::authorize('update', $report);

        if ($report->status !== 'pending_review') {
            return back()->with('error', 'Only pending documents can be approved.');
        }

        $report->update(['status' => 'approved']);

        Log::info('Document approved by manager', [
            'report_id' => $report->id,
            'manager_id' => request()->user()->id,
        ]);

        return back()->with('success', 'Document approved successfully.');
    }

    public function reject(Report $report): RedirectResponse
    {
        Gate::authorize('update', $report);

        if ($report->status !== 'pending_review') {
            return back()->with('error', 'Only pending documents can be rejected.');
        }

        $report->update(['status' => 'rejected']);

        Log::info('Document rejected by manager', [
            'report_id' => $report->id,
            'manager_id' => request()->user()->id,
        ]);

        return back()->with('success', 'Document rejected. The client will be notified to re-upload.');
    }

    public function financialIndex(Request $request): View
    {
        Gate::authorize('viewAny', Report::class);

        $companyId = $request->user()->company_id;

        $audits = Report::where('company_id', $companyId)
            ->where('name', 'not like', 'Standard_Report_%')
            ->latest()
            ->paginate(10);
            
        $clients = \App\Models\User::whereRelation('role', 'slug', 'client')
            ->where('company_id', $companyId)
            ->get();
            
        return view('reports.financial', compact('audits', 'clients'));
    }

    public function financialStore(Request $request): RedirectResponse
    {
        Gate::authorize('create', Report::class);

        $request->validate([
            'client_id' => 'required|exists:users,id',
        ]);

        $timezone = $request->input('client_timezone', 'UTC');

        $report = $this->provisionReport('Financial_Audit_', $request->user()->company_id, (int)$request->input('client_id'));
        GenerateHeavyReportJob::dispatch($report->id, $timezone);

        return back()->with('success', 'Heavy financial audit queued successfully.');
    }

    public function download(Report $report)
    {
        Gate::authorize('view', $report);

        if (!in_array($report->status, ['completed', 'approved', 'pending_review'])) {
            abort(404, 'The requested report is not ready or does not exist.');
        }

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('s3');

        if (!$disk->exists($report->file_path)) {
            abort(404, 'The file could not be found in the remote storage.');
        }

        Log::info('Downloading report directly through Laravel backend', [
            'report_id' => $report->id,
            'report_name' => $report->name,
            'user_id' => request()->user()->id,
            'company_id' => request()->user()->company_id,
        ]);

        $extension = pathinfo($report->file_path, PATHINFO_EXTENSION);
        $filename = $report->name . ($extension ? '.' . $extension : '');

        return $disk->download($report->file_path, $filename);
    }

    public function checkStatus(Report $report): JsonResponse
    { 
        Gate::authorize('view', $report);

        $isReady = in_array($report->status, ['completed', 'approved']);

        return response()->json([
            'status' => $report->status,
            'is_completed' => $isReady,
            'download_url' => $isReady ? URL::signedRoute('reports.download', $report) : null,
            'can_delete' => request()->user()->can('delete', $report),
            'delete_url' => route('reports.destroy', $report),
            'csrf_token' => csrf_token(), 
        ]);
    }

    public function destroy(Report $report): RedirectResponse
    {
        Gate::authorize('delete', $report);

        if ($report->file_path && Storage::disk('s3')->exists($report->file_path)) {
            Storage::disk('s3')->delete($report->file_path);
        }

        $report->delete();

        return back()->with('success', 'Report deleted successfully.');
    }

    private function provisionReport(string $prefix, string $companyId, ?int $clientId = null): Report
    {
        return Report::create([
            'company_id' => $companyId,
            'name' => $prefix . now()->format('Ymd_His'),
            'status' => 'pending',
            'processed_rows' => 0,
            'user_id' => $clientId ?? (request()->user()->hasRole('client') ? request()->user()->id : null),
        ]);
    }
}