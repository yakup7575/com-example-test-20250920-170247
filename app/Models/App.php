<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class App extends Model
{
    protected $fillable = [
        'name',
        'package_name',
        'bundle_id',
        'description',
        'website_url',
        'app_icon',
        'splash_image',
        'primary_color',
        'secondary_color',
        'platform',
        'status',
        'onesignal_app_id',
        'onesignal_api_key',
        'build_settings',
        'codemagic_build_id',
        'android_build_url',
        'ios_build_url',
        'last_build_at',
        'github_repo_url',
    ];

    protected $casts = [
        'build_settings' => 'array',
        'last_build_at' => 'datetime',
    ];

    public function menuItems(): HasMany
    {
        return $this->hasMany(MenuItem::class)->orderBy('order');
    }

    public function configurations(): HasMany
    {
        return $this->hasMany(AppConfiguration::class);
    }

    public function getConfiguration(string $key, $default = null)
    {
        $config = $this->configurations()->where('key', $key)->first();
        
        if (!$config) {
            return $default;
        }

        return match ($config->type) {
            'boolean' => (bool) $config->value,
            'integer' => (int) $config->value,
            'json' => json_decode($config->value, true),
            default => $config->value,
        };
    }

    public function setConfiguration(string $key, $value, string $type = 'string', string $description = null): void
    {
        $this->configurations()->updateOrCreate(
            ['key' => $key],
            [
                'value' => is_array($value) ? json_encode($value) : $value,
                'type' => $type,
                'description' => $description,
            ]
        );
    }
}
