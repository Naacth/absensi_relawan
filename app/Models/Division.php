<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    protected $fillable = ['name', 'description'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function schedules()
    {
        return $this->hasMany(WorkSchedule::class);
    }

    public function todaySchedule()
    {
        $dayOfWeek = now()->dayOfWeek; // 0 = Sunday, 1 = Monday, ...
        return $this->schedules()->where('day_of_week', $dayOfWeek)->first();
    }
}

