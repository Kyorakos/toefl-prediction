<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Batch;
use App\Models\ExamSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $registrationCode = $user->registrationCode;
        $batch = $registrationCode ? $registrationCode->batch : null;
        
        $currentExamSession = $user->getCurrentExamSession();
        $completedExams = $user->getCompletedExamSessions();
        
        $serverTime = Carbon::now('Asia/Jakarta');
        
        return view('student.dashboard', compact(
            'user',
            'batch',
            'currentExamSession',
            'completedExams',
            'serverTime'
        ));
    }

    public function profile()
    {
        return view('student.profile');
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . Auth::id(),
            'password' => 'nullable|string|min:6|regex:/^(?=.*[a-zA-Z])(?=.*\d)/',
        ]);

        $user = Auth::user();
        $user->name = $request->name;
        $user->email = $request->email;
        
        if ($request->filled('password')) {
            $user->password = $request->password; // Plain text as requested
        }
        
        $user->save();

        return back()->with('success', 'Profile updated successfully.');
    }
}
