<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'section',
        'question',
        'passage',
        'audio_file',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'correct_answer',
        'explanation',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function packages()
    {
        return $this->belongsToMany(QuestionPackage::class, 'package_questions', 'question_id', 'package_id')
            ->withPivot('order_number');
    }

    public function examAnswers()
    {
        return $this->hasMany(ExamAnswer::class);
    }

    public function getOptionsArray()
    {
        return [
            'a' => $this->option_a,
            'b' => $this->option_b,
            'c' => $this->option_c,
            'd' => $this->option_d,
        ];
    }

    public function hasAudio()
    {
        return $this->section === 'listening' && !empty($this->audio_file);
    }

    public function hasPassage()
    {
        return $this->section === 'reading' && !empty($this->passage);
    }

    public function getAudioUrl()
    {
        if (!$this->hasAudio()) {
            return null;
        }
        
        return asset('storage/audio/' . $this->audio_file);
    }
}
