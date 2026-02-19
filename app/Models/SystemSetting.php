<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];

    public static function getValue(string $key, mixed $default = null): mixed
    {
        $setting = static::query()->where('key', $key)->first();

        return $setting?->value ?? $default;
    }

    public static function getBool(string $key, bool $default = false): bool
    {
        $value = static::getValue($key, $default ? '1' : '0');

        return in_array(strtolower((string) $value), ['1', 'true', 'yes', 'on'], true);
    }

    public static function setValue(string $key, mixed $value): void
    {
        static::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value === null ? null : (string) $value]
        );
    }

    public static function setMany(array $pairs): void
    {
        foreach ($pairs as $key => $value) {
            static::setValue($key, $value);
        }
    }
}
