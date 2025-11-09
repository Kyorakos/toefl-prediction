<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ExamSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'batch_id',
        'question_package_id',
        'current_section',
        'current_question',
        'started_at',
        'listening_started_at',
        'structure_started_at',
        'reading_started_at',
        'completed_at',
        'status',
        'total_score',
        'listening_score',
        'structure_score',
        'reading_score',
        'tab_switch_count',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'listening_started_at' => 'datetime',
        'structure_started_at' => 'datetime',
        'reading_started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function questionPackage()
    {
        return $this->belongsTo(QuestionPackage::class);
    }

    public function examAnswers()
    {
        return $this->hasMany(ExamAnswer::class);
    }

    public function getCurrentQuestions()
    {
        return $this->questionPackage->questions()
            ->where('section', $this->current_section)
            ->get();
    }

    public function getRemainingTime()
    {
        $sectionStartTime = $this->getSectionStartTime();
        $sectionDuration = $this->getSectionDuration();
        
        if (!$sectionStartTime) {
            return $sectionDuration * 60; // Return full duration in seconds
        }
        
        $elapsed = Carbon::now()->diffInSeconds($sectionStartTime);
        $remaining = ($sectionDuration * 60) - $elapsed;
        
        return max(0, $remaining);
    }

    public function getSectionStartTime()
    {
        switch ($this->current_section) {
            case 'listening':
                return $this->listening_started_at;
            case 'structure':
                return $this->structure_started_at;
            case 'reading':
                return $this->reading_started_at;
            default:
                return null;
        }
    }

    public function getSectionDuration()
    {
        switch ($this->current_section) {
            case 'listening':
                return $this->questionPackage->listening_time;
            case 'structure':
                return $this->questionPackage->structure_time;
            case 'reading':
                return $this->questionPackage->reading_time;
            default:
                return 0;
        }
    }

    public function calculateScore()
    {
        $listeningCorrect = $this->examAnswers()
            ->whereHas('question', function ($query) {
                $query->where('section', 'listening');
            })
            ->where('is_correct', true)
            ->count();

        $structureCorrect = $this->examAnswers()
            ->whereHas('question', function ($query) {
                $query->where('section', 'structure');
            })
            ->where('is_correct', true)
            ->count();

        $readingCorrect = $this->examAnswers()
            ->whereHas('question', function ($query) {
                $query->where('section', 'reading');
            })
            ->where('is_correct', true)
            ->count();

        // TOEFL scoring conversion (simplified)
        $this->listening_score = $this->convertToToeflScore($listeningCorrect, 15);
        $this->structure_score = $this->convertToToeflScore($structureCorrect, 30);
        $this->reading_score = $this->convertToToeflScore($readingCorrect, 30);
        $this->total_score = $this->listening_score + $this->structure_score + $this->reading_score;
        
        $this->save();
    }

    private function convertToToeflScore($correct, $total)
    {
        $percentage = $correct / $total;
        return round($percentage * 100); // Simplified conversion
    }
}
