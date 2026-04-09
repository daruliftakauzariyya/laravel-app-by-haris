<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FatwaLike extends Model
{
    protected $fillable = ['fatwa_id', 'ip_address'];

    public function fatwa()
    {
        return $this->belongsTo(Fatwa::class);
    }
}
