<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'audio_id',
        'user_id',
        'message',
    ];

    public function audio()
    {
        return $this->belongsTo(Audio::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
