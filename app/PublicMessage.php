<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PublicMessage extends Model
{
    protected $table = 'public_messages';

    public function user() {
        return $this->belongsTo('App\User', 'user_id');
    }
}
