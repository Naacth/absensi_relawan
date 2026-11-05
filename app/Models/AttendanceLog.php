<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceLog extends Model
{
    // Tabel audit trail — hanya insert, tidak ada update
    public $timestamps = false;

    const CREATED_AT = 'created_at';

    protected $fillable = [
        'user_id',
        'type',
        'status',
        'reason',
        'match_score',
        'liveness_passed',
        'ip_address',
        'user_agent',
        'client_timestamp',
    ];

    protected function casts(): array
    {
        return [
            'liveness_passed'  => 'boolean',
            'created_at'       => 'datetime',
            'client_timestamp' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
