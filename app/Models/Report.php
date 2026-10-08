<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class Report extends Model
{
    use HasFactory, SoftDeletes, Prunable, HasUuids;

    protected $fillable = [
        'company_id',
        'user_id',
        'name',
        'status',
        'processed_rows',
        'file_path',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function prunable(): Builder
    {
        return static::where('created_at', '<=', now()->subDays(30));
    }

    protected function pruning(): void
    {
        if ($this->file_path) {
            Storage::disk('s3')->delete($this->file_path);
            
            Log::info("Prunable: Deleted physical report file from S3.", [
                'report_id' => $this->id,
                'file_path' => $this->file_path
            ]);
        }
    }
}