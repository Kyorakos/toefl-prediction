<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class QuestionController extends Controller
{
    public function index(Request $request)
    {
        $query = Question::query();

        if ($request->has('section') && $request->section !== '') {
            $query->where('section', $request->section);
        }

        if ($request->has('search') && $request->search !== '') {
            $query->where('question', 'like', '%' . $request->search . '%');
        }

        $questions = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.questions.index', compact('questions'));
    }

    public function create()
    {
        return view('admin.questions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'section' => 'required|in:listening,structure,reading',
            'question' => 'required|string',
            'passage' => 'nullable|string',
            'audio_file' => 'nullable|mimes:mp3,wav,ogg|max:10240',
            'option_a' => 'required|string',
            'option_b' => 'required|string',
            'option_c' => 'required|string',
            'option_d' => 'required|string',
            'correct_answer' => 'required|in:a,b,c,d',
            'explanation' => 'nullable|string',
        ]);

        $data = $request->all();

        if ($request->hasFile('audio_file')) {
            $data['audio_file'] = $request->file('audio_file')->store('audio', 'public');
        }

        Question::create($data);

        return redirect()->route('admin.questions.index')
            ->with('success', 'Question created successfully.');
    }

    public function show(Question $question)
    {
        return view('admin.questions.show', compact('question'));
    }

    public function edit(Question $question)
    {
        return view('admin.questions.edit', compact('question'));
    }

    public function update(Request $request, Question $question)
    {
        $request->validate([
            'section' => 'required|in:listening,structure,reading',
            'question' => 'required|string',
            'passage' => 'nullable|string',
            'audio_file' => 'nullable|mimes:mp3,wav,ogg|max:10240',
            'option_a' => 'required|string',
            'option_b' => 'required|string',
            'option_c' => 'required|string',
            'option_d' => 'required|string',
            'correct_answer' => 'required|in:a,b,c,d',
            'explanation' => 'nullable|string',
        ]);

        $data = $request->all();

        if ($request->hasFile('audio_file')) {
            // Delete old file if exists
            if ($question->audio_file && Storage::disk('public')->exists($question->audio_file)) {
                Storage::disk('public')->delete($question->audio_file);
            }
            
            $data['audio_file'] = $request->file('audio_file')->store('audio', 'public');
        }

        $question->update($data);

        return redirect()->route('admin.questions.index')
            ->with('success', 'Question updated successfully.');
    }

    public function destroy(Question $question)
    {
        // Delete audio file if exists
        if ($question->audio_file && Storage::disk('public')->exists($question->audio_file)) {
            Storage::disk('public')->delete($question->audio_file);
        }

        $question->delete();

        return redirect()->route('admin.questions.index')
            ->with('success', 'Question deleted successfully.');
    }
}
