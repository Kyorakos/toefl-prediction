<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Batch;
use App\Models\QuestionPackage;
use App\Models\ExamSession;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalStudents = User::where('role', 'student')->count();
        $activeBatches = Batch::where('is_active', true)->count();
        $totalPackages = QuestionPackage::count();
        $completedExams = ExamSession::where('status', 'completed')->count();
        
        $recentResults = ExamSession::with(['user', 'batch'])
            ->where('status', 'completed')
            ->orderBy('completed_at', 'desc')
            ->take(10)
            ->get();
            
        $upcomingBatches = Batch::where('start_time', '>', now())
            ->where('is_active', true)
            ->orderBy('start_time')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalStudents',
            'activeBatches',
            'totalPackages',
            'completedExams',
            'recentResults',
            'upcomingBatches'
        ));
    }
}
