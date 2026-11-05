<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'start_date',
        'end_date',
        'reason',
        'attachment_path',
        'status',
        'reviewed_by',
        'reviewed_at',
        'rejection_note',
    ];

    protected function casts(): array
    {
        return [
            'start_date'  => 'date',
            'end_date'    => 'date',
            'reviewed_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}

