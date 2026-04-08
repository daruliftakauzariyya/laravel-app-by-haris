<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = ['key', 'value', 'type'];

    public static function get($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        if (!$setting) return $default;

        $value = $setting->value;
        if ($setting->type === 'integer') return (int) $value;
        if ($setting->type === 'boolean') return (bool) filter_var($value, FILTER_VALIDATE_BOOLEAN);
        
        return $value;
    }

    public static function set($key, $value, $type = 'string')
    {
        return self::updateOrCreate(
            ['key' => $key],
            ['value' => (string) $value, 'type' => $type]
        );
    }
}
