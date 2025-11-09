<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\QuestionPackage;
use App\Models\RegistrationCode;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BatchController extends Controller
{
    public function index()
    {
        $batches = Batch::with(['questionPackage', 'examSessions'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.batches.index', compact('batches'));
    }

    public function create()
    {
        $packages = QuestionPackage::where('is_active', true)->get();
        return view('admin.batches.create', compact('packages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'question_package_id' => 'required|exists:question_packages,id',
            'start_time' => 'required|date|after:now',
            'end_time' => 'required|date|after:start_time',
            'participant_count' => 'required|integer|min:1|max:1000',
        ]);

        $batch = Batch::create([
            'name' => $request->name,
            'description' => $request->description,
            'question_package_id' => $request->question_package_id,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ]);

        // Generate registration codes
        for ($i = 0; $i < $request->participant_count; $i++) {
            RegistrationCode::create([
                'code' => $this->generateUniqueCode(),
                'batch_id' => $batch->id,
            ]);
        }

        return redirect()->route('admin.batches.index')
            ->with('success', 'Batch created successfully with ' . $request->participant_count . ' registration codes.');
    }

    public function show(Batch $batch)
    {
        $batch->load(['questionPackage', 'registrationCodes', 'examSessions.user']);
        return view('admin.batches.show', compact('batch'));
    }

    public function edit(Batch $batch)
    {
        $packages = QuestionPackage::where('is_active', true)->get();
        return view('admin.batches.edit', compact('batch', 'packages'));
    }

    public function update(Request $request, Batch $batch)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'question_package_id' => 'required|exists:question_packages,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
        ]);

        $batch->update($request->only([
            'name',
            'description',
            'question_package_id',
            'start_time',
            'end_time',
        ]));

        return redirect()->route('admin.batches.index')
            ->with('success', 'Batch updated successfully.');
    }

    public function destroy(Batch $batch)
    {
        $batch->delete();

        return redirect()->route('admin.batches.index')
            ->with('success', 'Batch deleted successfully.');
    }

    public function generateCodes(Request $request, Batch $batch)
    {
        $request->validate([
            'count' => 'required|integer|min:1|max:100',
        ]);

        for ($i = 0; $i < $request->count; $i++) {
            RegistrationCode::create([
                'code' => $this->generateUniqueCode(),
                'batch_id' => $batch->id,
            ]);
        }

        return back()->with('success', $request->count . ' registration codes generated successfully.');
    }

    private function generateUniqueCode()
    {
        do {
            $code = 'REG' . strtoupper(Str::random(8));
        } while (RegistrationCode::where('code', $code)->exists());

        return $code;
    }
}
