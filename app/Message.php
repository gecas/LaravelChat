<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
	protected $fillable = ['send_to', 'send_from', 'message'];

    public function send_to()
    {
        return $this->belongsTo('App\User');
    }

    public function send_from()
    {
        return $this->belongsTo('App\User');
    }
}
