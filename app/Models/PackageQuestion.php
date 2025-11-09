<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'package_id',
        'question_id',
        'order_number',
    ];

    public function package()
    {
        return $this->belongsTo(QuestionPackage::class, 'package_id');
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}