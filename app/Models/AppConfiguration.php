<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppConfiguration extends Model
{
    protected $fillable = [
        'app_id',
        'key',
        'value',
        'type',
        'description',
    ];

    public function app(): BelongsTo
    {
        return $this->belongsTo(App::class);
    }

    public function getTypedValueAttribute()
    {
        return match ($this->type) {
            'boolean' => (bool) $this->value,
            'integer' => (int) $this->value,
            'json' => json_decode($this->value, true),
            default => $this->value,
        };
    }

    public function setTypedValue($value): void
    {
        $this->value = is_array($value) ? json_encode($value) : $value;
    }
}
