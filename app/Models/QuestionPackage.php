<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'listening_questions',
        'structure_questions',
        'reading_questions',
        'listening_time',
        'structure_time',
        'reading_time',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function questions()
    {
        return $this->belongsToMany(Question::class, 'package_questions', 'package_id', 'question_id')
            ->withPivot('order_number')
            ->orderBy('package_questions.order_number');
    }

    public function packageQuestions()
    {
        return $this->hasMany(PackageQuestion::class, 'package_id');
    }

    public function batches()
    {
        return $this->hasMany(Batch::class);
    }

    public function getListeningQuestions()
    {
        return $this->questions()->where('section', 'listening');
    }

    public function getStructureQuestions()
    {
        return $this->questions()->where('section', 'structure');
    }

    public function getReadingQuestions()
    {
        return $this->questions()->where('section', 'reading');
    }

    public function getTotalQuestions()
    {
        return $this->questions()->count();
    }

    public function getCompletionPercentage()
    {
        $totalRequired = $this->listening_questions + $this->structure_questions + $this->reading_questions;
        $totalAssigned = $this->getTotalQuestions();
        
        if ($totalRequired === 0) return 0;
        
        return round(($totalAssigned / $totalRequired) * 100, 2);
    }

    public function isComplete()
    {
        return $this->getCompletionPercentage() === 100.0;
    }
}
