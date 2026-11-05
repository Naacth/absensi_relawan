<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FaceEmbedding extends Model
{
    protected $fillable = [
        'user_id',
        'embedding',
        'photo_path',
    ];

    protected $hidden = [
        'embedding', // jangan expose vektor wajah di JSON response
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
