<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Batch extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'question_package_id',
        'start_time',
        'end_time',
        'is_active',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function questionPackage()
    {
        return $this->belongsTo(QuestionPackage::class);
    }

    public function registrationCodes()
    {
        return $this->hasMany(RegistrationCode::class);
    }

    public function examSessions()
    {
        return $this->hasMany(ExamSession::class);
    }

    public function participants()
    {
        return $this->hasManyThrough(User::class, RegistrationCode::class, 'batch_id', 'id', 'id', 'used_by');
    }

    public function isActive()
    {
        return $this->is_active && Carbon::now()->between($this->start_time, $this->end_time);
    }

    public function canStart()
    {
        return $this->is_active && Carbon::now()->diffInMinutes($this->start_time, false) <= 5;
    }

    public function hasStarted()
    {
        return Carbon::now()->greaterThan($this->start_time);
    }

    public function hasEnded()
    {
        return Carbon::now()->greaterThan($this->end_time);
    }

    public function getStatusAttribute()
    {
        if ($this->hasEnded()) {
            return 'completed';
        } elseif ($this->hasStarted()) {
            return 'in_progress';
        } elseif ($this->canStart()) {
            return 'ready';
        } else {
            return 'waiting';
        }
    }
}
