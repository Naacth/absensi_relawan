<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    protected $fillable = ['name', 'description', 'daily_rate', 'daily_transport', 'meal_allowance', 'overtime_hourly_rate', 'late_deduction'];

    protected function casts(): array
    {
        return [
            'daily_rate'           => 'decimal:2',
            'daily_transport'      => 'decimal:2',
            'meal_allowance'       => 'decimal:2',
            'overtime_hourly_rate' => 'decimal:2',
            'late_deduction'       => 'decimal:2',
        ];
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function schedules()
    {
        return $this->hasMany(WorkSchedule::class);
    }

    /**
     * Ambil jadwal yang berlaku hari ini.
     * Prioritas: jadwal tunggal (day_of_week = NULL) → jadwal spesifik hari ini.
     */
    public function todaySchedule()
    {
        // Cek jadwal tunggal (berlaku semua hari)
        $single = $this->schedules()->whereNull('day_of_week')->first();
        if ($single) {
            return $single;
        }

        // Fallback: jadwal spesifik per hari (0=Minggu, 1=Senin, ...)
        return $this->schedules()->where('day_of_week', now()->dayOfWeek)->first();
    }
}
