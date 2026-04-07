<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FatwaViewSnapshot extends Model
{
    protected $table = 'fatwa_view_snapshots';

    protected $fillable = ['fatwa_id', 'views', 'snapshot_date'];

    protected $casts = ['snapshot_date' => 'date'];

    public function fatwa()
    {
        return $this->belongsTo(Fatwa::class);
    }
}
