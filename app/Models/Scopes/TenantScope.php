<?php

declare(strict_types=1);

namespace App\Models\Scopes;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if ($model instanceof User || app()->runningInConsole()) {
            return;
        }

        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if (!$user) {
            return;
        }

        if (method_exists($user, 'isSuperAdmin') && !$user->isSuperAdmin()) {
            $builder->where($model->getTable() . '.company_id', $user->company_id);
        }
    }
}