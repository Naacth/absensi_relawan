<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryScheme extends Model
{
    protected $fillable = [
        'name',
        'hourly_rate',
        'daily_transport',
        'meal_allowance',
        'overtime_hourly_rate',
        'late_deduction',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'hourly_rate'          => 'decimal:2',
            'daily_transport'      => 'decimal:2',
            'meal_allowance'       => 'decimal:2',
            'overtime_hourly_rate' => 'decimal:2',
            'late_deduction'       => 'decimal:2',
            'is_active'            => 'boolean',
        ];
    }
}

