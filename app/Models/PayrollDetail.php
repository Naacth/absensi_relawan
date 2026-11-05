<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollDetail extends Model
{
    protected $fillable = [
        'payroll_id',
        'user_id',
        'total_days_attended',
        'total_days_leave',
        'total_hours',
        'base_salary',
        'transport_allowance',
        'meal_allowance',
        'overtime_pay',
        'deductions',
        'net_salary',
        'payment_status',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'total_days_attended' => 'integer',
            'total_days_leave'    => 'integer',
            'total_hours'         => 'decimal:2',
            'base_salary'         => 'decimal:2',
            'transport_allowance' => 'decimal:2',
            'meal_allowance'      => 'decimal:2',
            'overtime_pay'        => 'decimal:2',
            'deductions'          => 'decimal:2',
            'net_salary'          => 'decimal:2',
            'paid_at'             => 'datetime',
        ];
    }

    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

