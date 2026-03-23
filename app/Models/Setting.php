<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    /** @use HasFactory<\Database\Factories\SettingFactory> */
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'social' => 'array',
        'smtp' => 'array',
        'other' => 'array',
    ];

    public static function current(): self
    {
        return static::query()->first() ?? new static(static::defaults());
    }

    public static function defaults(): array
    {
        $appName = (string) config('app.name', 'DOCUMATE');

        return [
            'bname' => $appName,
            'email' => '',
            'phone' => '',
            'whatsapp' => '',
            'currency' => 'PHP',
            'address' => '',
            'logo' => null,
            'meta_title' => $appName,
            'meta_description' => '',
            'meta_keywords' => '',
            'social' => [],
            'smtp' => [],
            'other' => [],
            'map' => null,
            'header' => null,
            'footer' => null,
        ];
    }
}
