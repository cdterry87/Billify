<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    protected $fillable = [
        'name', 'description', 'amount', 'day', 'user_id',
        'month', 'january', 'february', 'march', 'april', 'may', 'june',
        'july', 'august', 'september', 'october', 'november', 'december'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
