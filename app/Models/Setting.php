<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings';

    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['key', 'value'];

    public static function get(string $key, $default = null)
    {
        $row = static::where('key', $key)->first();

        return $row !== null ? $row->value : $default;
    }

    public static function set(string $key, $value): void
    {
        $v = $value === null || $value === '' ? null : (string) $value;
        static::updateOrCreate(['key' => $key], ['value' => $v]);
    }
}
