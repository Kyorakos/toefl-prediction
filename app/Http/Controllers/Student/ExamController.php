<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ExamSession;
use App\Models\ExamAnswer;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExamController extends Controller
{
    public function start(Request $request)
    {
        $user = Auth::user();
        $registrationCode = $user->registrationCode;
        
        if (!$registrationCode || !$registrationCode->batch) {
            return redirect()->route('student.dashboard')
                ->with('error', 'No active batch found.');
        }

        $batch = $registrationCode->batch;
        
        if (!$batch->canStart()) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Exam is not available yet.');
        }

        $existingSession = ExamSession::where('user_id', $user->id)
            ->where('batch_id', $batch->id)
            ->first();

        if ($existingSession && $existingSession->status === 'completed') {
            return redirect()->route('student.dashboard')
                ->with('error', 'You have already completed this exam.');
        }

        if (!$existingSession) {
            $existingSession = ExamSession::create([
                'user_id' => $user->id,
                'batch_id' => $batch->id,
                'question_package_id' => $batch->question_package_id,
                'status' => 'not_started',
            ]);
        }

        return view('student.exam.start', compact('batch', 'existingSession'));
    }

    public function begin(Request $request)
    {
        $user = Auth::user();
        $sessionId = $request->session_id;
        
        $examSession = ExamSession::where('id', $sessionId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        if ($examSession->status === 'completed') {
            return redirect()->route('student.dashboard');
        }

        if ($examSession->status === 'not_started') {
            $examSession->update([
                'status' => 'in_progress',
                'started_at' => Carbon::now(),
                'listening_started_at' => Carbon::now(),
            ]);
        }

        return redirect()->route('student.exam.take', $examSession->id);
    }

    public function take(ExamSession $examSession)
    {
        $user = Auth::user();
        
        if ($examSession->user_id !== $user->id) {
            abort(403);
        }

        if ($examSession->status === 'completed') {
            return redirect()->route('student.dashboard');
        }

        $questions = $examSession->questionPackage->questions()
            ->where('section', $examSession->current_section)
            ->orderBy('package_questions.order_number')
            ->get();

        $currentQuestion = $questions->skip($examSession->current_question - 1)->first();
        
        $answers = ExamAnswer::where('exam_session_id', $examSession->id)
            ->pluck('selected_answer', 'question_id');

        $remainingTime = $examSession->getRemainingTime();

        return view('student.exam.take', compact(
            'examSession',
            'questions',
            'currentQuestion',
            'answers',
            'remainingTime'
        ));
    }

    public function saveAnswer(Request $request, ExamSession $examSession)
    {
        $request->validate([
            'question_id' => 'required|exists:questions,id',
            'answer' => 'required|in:a,b,c,d',
        ]);

        $user = Auth::user();
        
        if ($examSession->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $question = Question::findOrFail($request->question_id);
        $isCorrect = $question->correct_answer === $request->answer;

        ExamAnswer::updateOrCreate(
            [
                'exam_session_id' => $examSession->id,
                'question_id' => $request->question_id,
            ],
            [
                'selected_answer' => $request->answer,
                'is_correct' => $isCorrect,
                'answered_at' => Carbon::now(),
            ]
        );

        return response()->json(['success' => true]);
    }

    public function navigate(Request $request, ExamSession $examSession)
    {
        $request->validate([
            'direction' => 'required|in:prev,next',
        ]);

        $user = Auth::user();
        
        if ($examSession->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $questions = $examSession->questionPackage->questions()
            ->where('section', $examSession->current_section)
            ->orderBy('package_questions.order_number')
            ->get();

        $currentQuestion = $examSession->current_question;
        $totalQuestions = $questions->count();

        if ($request->direction === 'next' && $currentQuestion < $totalQuestions) {
            $examSession->update(['current_question' => $currentQuestion + 1]);
        } elseif ($request->direction === 'prev' && $currentQuestion > 1) {
            $examSession->update(['current_question' => $currentQuestion - 1]);
        }

        return response()->json(['success' => true]);
    }

    public function nextSection(ExamSession $examSession)
    {
        $user = Auth::user();
        
        if ($examSession->user_id !== $user->id) {
            abort(403);
        }

        $sectionOrder = ['listening', 'structure', 'reading'];
        $currentIndex = array_search($examSession->current_section, $sectionOrder);
        
        if ($currentIndex < count($sectionOrder) - 1) {
            $nextSection = $sectionOrder[$currentIndex + 1];
            $startTimeField = $nextSection . '_started_at';
            
            $examSession->update([
                'current_section' => $nextSection,
                'current_question' => 1,
                $startTimeField => Carbon::now(),
            ]);
            
            return redirect()->route('student.exam.take', $examSession->id);
        } else {
            return $this->submitExam($examSession);
        }
    }

    public function submitExam(ExamSession $examSession)
    {
        $user = Auth::user();
        
        if ($examSession->user_id !== $user->id) {
            abort(403);
        }

        $examSession->update([
            'status' => 'completed',
            'completed_at' => Carbon::now(),
        ]);

        $examSession->calculateScore();

        return redirect()->route('student.exam.completed', $examSession->id);
    }

    public function completed(ExamSession $examSession)
    {
        $user = Auth::user();
        
        if ($examSession->user_id !== $user->id) {
            abort(403);
        }

        if ($examSession->status !== 'completed') {
            return redirect()->route('student.dashboard');
        }

        return view('student.exam.completed', compact('examSession'));
    }

    public function checkTabSwitch(Request $request, ExamSession $examSession)
    {
        $user = Auth::user();
        
        if ($examSession->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $examSession->increment('tab_switch_count');
        $user->increment('tab_switch_count');

        if ($examSession->tab_switch_count >= 3) {
            $examSession->update([
                'status' => 'completed',
                'completed_at' => Carbon::now(),
            ]);
            
            return response()->json([
                'auto_submit' => true,
                'message' => 'Exam submitted due to multiple tab switches.'
            ]);
        }

        return response()->json([
            'warning' => true,
            'count' => $examSession->tab_switch_count,
            'remaining' => 3 - $examSession->tab_switch_count
        ]);
    }

    public function syncTimer(Request $request, ExamSession $examSession)
    {
        $user = Auth::user();
        
        if ($examSession->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $remainingTime = $examSession->getRemainingTime();
        
        if ($remainingTime <= 0) {
            return response()->json([
                'time_up' => true,
                'remaining' => 0
            ]);
        }

        return response()->json([
            'remaining' => $remainingTime,
            'time_up' => false
        ]);
    }
}
