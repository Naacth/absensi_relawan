<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    protected $fillable = [
        'title',
        'period_start',
        'period_end',
        'status',
        'total_disbursed',
        'created_by',
        'approved_by',
        'approved_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'period_start'    => 'date',
            'period_end'      => 'date',
            'total_disbursed' => 'decimal:2',
            'approved_at'     => 'datetime',
        ];
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function details()
    {
        return $this->hasMany(PayrollDetail::class);
    }
}

