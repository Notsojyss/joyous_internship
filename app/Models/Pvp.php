<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
class Pvp extends Model
{
    protected $table = 'pvp';

    public function user()
    {
        return $this->belongsTo(User::class, 'host_id');
    }
}
