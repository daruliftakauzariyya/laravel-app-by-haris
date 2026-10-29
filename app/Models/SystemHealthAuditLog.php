<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemHealthAuditLog extends Model
{
    protected $table = 'system_health_audit_logs';

    protected $fillable = [
        'event',
        'email',
        'ip_address',
        'user_agent',
        'details',
    ];

    protected $casts = [
        'details' => 'array',
    ];
}

