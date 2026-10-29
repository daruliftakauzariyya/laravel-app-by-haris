<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemApiLog extends Model
{
    protected $table = 'system_api_logs';

    public $timestamps = false;

    protected $fillable = [
        'correlation_id',
        'method',
        'path',
        'status_code',
        'duration_ms',
        'source_category',
        'is_synthetic',
        'error_category',
        'ip_address',
        'created_at',
    ];

    protected $casts = [
        'status_code'   => 'integer',
        'duration_ms'   => 'float',
        'is_synthetic'  => 'boolean',
        'created_at'    => 'datetime',
    ];
}

