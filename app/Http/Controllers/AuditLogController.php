<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(): View
    {
        $logs = AuditLog::with(['user', 'company', 'auditable'])
            ->latest()
            ->paginate(20);

        return view('audit-logs.index', compact('logs'));
    }
}