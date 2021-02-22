<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Audio extends Model
{
    use HasFactory;

    protected $table    = 'audio';

    protected $appends  = [
        'file_url'
    ];

    protected $fillable = [
        'user_id',
        'description',
        'title',
        'private',
        'download',
        'waveform_path',
        'file_path',
        'uuid',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function getFileUrlAttribute() {
        return Storage::disk('audio')->url($this->file_path);
    }

    public function scopeLoadData($query) {
        $query->with('user');

        return $query;
    }
}
