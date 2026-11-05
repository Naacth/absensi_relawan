<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'nik',
        'phone',
        'address',
        'email',
        'password',
        'role',
        'division_id',
        'is_active',
        'face_enrolled',
        'face_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
            'face_enrolled'     => 'boolean',
        ];
    }

    // Relasi
    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function faceEmbedding()
    {
        return $this->hasOne(FaceEmbedding::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function attendanceLogs()
    {
        return $this->hasMany(AttendanceLog::class);
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function payrollDetails()
    {
        return $this->hasMany(PayrollDetail::class);
    }

    // Role Helpers
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['super_admin', 'admin']);
    }

    public function isRelawan(): bool
    {
        return $this->role === 'relawan';
    }

    public function todayAttendance()
    {
        return $this->attendances()->whereDate('date', today())->first();
    }
}
