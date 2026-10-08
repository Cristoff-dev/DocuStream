<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class PortalController extends Controller
{
    public function index(Request $request): View
    {
        $tenant = $request->user()->company;
        $user = $request->user();
        
        $metrics = [
            'active_users' => $tenant->users()->count(),
            'total_reports' => $tenant->reports()->count(),
            'last_sync' => now()->subMinutes(rand(1, 15))->diffForHumans(),
        ];

        $reportsQuery = $tenant->reports()->latest();
        
        if ($user->hasRole('client')) {
            $reportsQuery->where('user_id', $user->id);
        }

        $reports = $reportsQuery->paginate(10);

        return view('client.portal', compact('tenant', 'metrics', 'reports'));
    }
}