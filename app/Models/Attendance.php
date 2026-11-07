<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'check_in_at',
        'check_in_score',
        'check_in_liveness',
        'check_in_snapshot',
        'check_in_lat',
        'check_in_lng',
        'distance_m',
        'photo_check_in_path',
        'check_out_at',
        'check_out_score',
        'check_out_liveness',
        'check_out_snapshot',
        'check_out_lat',
        'check_out_lng',
        'distance_out_m',
        'photo_check_out_path',
        'total_hours',
        'is_manual',
        'manual_by',
        'manual_note',
    ];

    protected function casts(): array
    {
        return [
            'date'              => 'date',
            'check_in_at'       => 'datetime',
            'check_out_at'      => 'datetime',
            'check_in_liveness' => 'boolean',
            'check_out_liveness'=> 'boolean',
            'check_in_lat'      => 'decimal:7',
            'check_in_lng'      => 'decimal:7',
            'distance_m'        => 'decimal:2',
            'check_out_lat'     => 'decimal:7',
            'check_out_lng'     => 'decimal:7',
            'distance_out_m'    => 'decimal:2',
            'total_hours'       => 'decimal:2',
            'is_manual'         => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function calculateTotalHours(): void
    {
        if ($this->check_in_at && $this->check_out_at) {
            $diffInMinutes = $this->check_in_at->diffInMinutes($this->check_out_at);
            $this->total_hours = round($diffInMinutes / 60, 2);
            $this->save();
        }
    }
}
