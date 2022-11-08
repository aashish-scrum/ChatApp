<?php

namespace App\Traits;

use App\Models\User;

trait Messaging
{

    public function getCreatedAtAttribute($value)
    {
        return $this->created_at = date('D, h:iA',strtotime($value));
    }

    public function receiver()
    {
        return $this->belongsTo(User::class,'receiver_id','id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class,'sender_id','id');
    }

}