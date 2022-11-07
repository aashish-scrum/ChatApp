<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','message'];

    public function getCreatedAtAttribute($value)
    {
        return $this->created_at = date('D, h:iA',strtotime($value));
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
