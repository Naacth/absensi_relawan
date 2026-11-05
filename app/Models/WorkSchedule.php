<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkSchedule extends Model
{
    protected $fillable = [
        'division_id',
        'day_of_week',
        'check_in_start',
        'check_in_end',
        'check_out_start',
        'check_out_end',
        'late_tolerance',
    ];

    protected function casts(): array
    {
        return [
            'day_of_week'    => 'integer',
            'late_tolerance' => 'integer',
        ];
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function getDayNameAttribute(): string
    {
        $days = [
            0 => 'Minggu',
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
        ];
        return $days[$this->day_of_week] ?? '-';
    }
}

