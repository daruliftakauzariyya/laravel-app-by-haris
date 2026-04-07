<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FatwaUserView extends Model
{
    protected $table = 'fatwa_user_views';

    protected $fillable = ['fatwa_id', 'user_id', 'views'];

    public function fatwa()
    {
        return $this->belongsTo(Fatwa::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
