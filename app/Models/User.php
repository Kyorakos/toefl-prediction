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
        'email',
        'password',
        'registration_code',
        'role',
        'status',
        'tab_switch_count',
        'last_activity',
    ];

    protected $hidden = [
        'remember_token',
    ];

    protected $casts = [
        'last_activity' => 'datetime',
    ];

    public function registrationCode()
    {
        return $this->belongsTo(RegistrationCode::class, 'registration_code', 'code');
    }

    public function examSessions()
    {
        return $this->hasMany(ExamSession::class);
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isStudent()
    {
        return $this->role === 'student';
    }

    public function getCurrentExamSession()
    {
        return $this->examSessions()
            ->whereIn('status', ['not_started', 'in_progress'])
            ->latest()
            ->first();
    }

    public function getCompletedExamSessions()
    {
        return $this->examSessions()
            ->whereIn('status', ['completed', 'submitted'])
            ->get();
    }
}
