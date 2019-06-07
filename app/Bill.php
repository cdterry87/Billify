<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    protected $fillable = [
        'name', 'description', 'amount', 'day', 'user_id'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
