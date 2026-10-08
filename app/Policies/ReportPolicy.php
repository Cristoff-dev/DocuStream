<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Report;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ReportPolicy
{
    public function viewAny(User $user): Response
    {
        return Response::allow();
    }

    public function view(User $user, Report $report): Response
    {
        if ($user->company_id !== $report->company_id) {
            return Response::denyAsNotFound('Document not found.');
        }

        if ($user->hasRole('client') && $report->user_id !== null) {
            if ($user->id !== $report->user_id) {
                return Response::denyAsNotFound('Document not found.');
            }
        }

        return Response::allow();
    }

    public function create(User $user): Response
    {
        if ($user->hasRole('client')) {
            return Response::allow();
        }

        if ($user->hasRole('manager')) {
            if (empty($user->permissions) || $user->hasPermission('request_heavy_audits')) {
                return Response::allow();
            }
        }

        return Response::deny('Unauthorized to provision documents.');
    }

    public function update(User $user, Report $report): Response
    {
        if ($user->company_id !== $report->company_id) {
            return Response::denyAsNotFound('Document not found.');
        }

        if ($user->hasRole('manager')) {
            return Response::allow();
        }

        return Response::denyAsNotFound('Document not found.');
    }

    public function delete(User $user, Report $report): Response
    {
        if ($user->company_id !== $report->company_id) {
            return Response::denyAsNotFound('Document not found.');
        }

        if ($user->hasRole('manager')) {
            if (empty($user->permissions) || $user->hasPermission('delete_reports')) {
                return Response::allow();
            }
        }

        return Response::denyAsNotFound('Document not found.');
    }
}