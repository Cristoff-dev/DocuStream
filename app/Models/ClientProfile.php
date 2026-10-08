<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientProfile extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'legal_name',
        'contact_email',
        'tax_id',
        'contact_phone',
        'country_code',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}