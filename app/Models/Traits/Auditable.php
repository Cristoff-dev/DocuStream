<?php

declare(strict_types=1);

namespace App\Models\Traits;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

/**
 * @mixin \Illuminate\Database\Eloquent\Model
 * @property int|string $id
 */
trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(fn (Model $model) => $model->logAction('created'));
        static::updated(fn (Model $model) => $model->logAction('updated'));
        static::deleted(fn (Model $model) => $model->logAction('deleted'));
    }

    protected function logAction(string $action): void
    {
        if (!Auth::check()) {
            return;
        }

        $oldValues = $action === 'updated' || $action === 'deleted' ? $this->getOriginal() : [];
        $newValues = $action === 'updated' || $action === 'created' ? $this->getAttributes() : [];

        $oldValues = Arr::except($oldValues, ['password']);
        $newValues = Arr::except($newValues, ['password']);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $companyId = $this->getAttribute('company_id') ?? $user->company_id;

        AuditLog::create([
            'user_id' => $user->id,
            'company_id' => $companyId,
            'action' => $action,
            'model_type' => static::class,
            'model_id' => $this->id,
            'metadata' => [
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'user_agent' => request()->userAgent(),
            ],
            'ip_address' => request()->ip(),
        ]);
    }
}