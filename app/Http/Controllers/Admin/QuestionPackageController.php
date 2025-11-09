<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuestionPackage;
use App\Models\Question;
use App\Models\PackageQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuestionPackageController extends Controller
{
    public function index()
    {
        $packages = QuestionPackage::withCount('questions')->paginate(10);
        return view('admin.packages.index', compact('packages'));
    }

    public function create()
    {
        return view('admin.packages.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'listening_questions' => 'required|integer|min:1|max:50',
            'structure_questions' => 'required|integer|min:1|max:50',
            'reading_questions' => 'required|integer|min:1|max:50',
            'listening_time' => 'required|integer|min:1|max:120',
            'structure_time' => 'required|integer|min:1|max:120',
            'reading_time' => 'required|integer|min:1|max:120',
        ]);

        QuestionPackage::create($request->all());

        return redirect()->route('admin.packages.index')
            ->with('success', 'Question package created successfully.');
    }

    public function show(QuestionPackage $package)
    {
        $package->load(['questions' => function ($query) {
            $query->orderBy('package_questions.order_number');
        }]);

        return view('admin.packages.show', compact('package'));
    }

    public function edit(QuestionPackage $package)
    {
        return view('admin.packages.edit', compact('package'));
    }

    public function update(Request $request, QuestionPackage $package)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'listening_questions' => 'required|integer|min:1|max:50',
            'structure_questions' => 'required|integer|min:1|max:50',
            'reading_questions' => 'required|integer|min:1|max:50',
            'listening_time' => 'required|integer|min:1|max:120',
            'structure_time' => 'required|integer|min:1|max:120',
            'reading_time' => 'required|integer|min:1|max:120',
        ]);

        $package->update($request->all());

        return redirect()->route('admin.packages.index')
            ->with('success', 'Question package updated successfully.');
    }

    public function destroy(QuestionPackage $package)
    {
        $package->delete();

        return redirect()->route('admin.packages.index')
            ->with('success', 'Question package deleted successfully.');
    }

    public function builder(QuestionPackage $package)
    {
        $questions = Question::where('is_active', true)->get();
        $packageQuestions = $package->questions()->orderBy('package_questions.order_number')->get();

        return view('admin.packages.builder', compact('package', 'questions', 'packageQuestions'));
    }

    public function addQuestion(Request $request, QuestionPackage $package)
    {
        $request->validate([
            'question_id' => 'required|exists:questions,id',
            'order_number' => 'required|integer|min:1',
        ]);

        $existingOrder = PackageQuestion::where('package_id', $package->id)
            ->where('order_number', $request->order_number)
            ->first();

        if ($existingOrder) {
            return back()->with('error', 'Order number already exists in this package.');
        }

        PackageQuestion::create([
            'package_id' => $package->id,
            'question_id' => $request->question_id,
            'order_number' => $request->order_number,
        ]);

        return back()->with('success', 'Question added to package successfully.');
    }

    public function removeQuestion(QuestionPackage $package, Question $question)
    {
        PackageQuestion::where('package_id', $package->id)
            ->where('question_id', $question->id)
            ->delete();

        return back()->with('success', 'Question removed from package successfully.');
    }

    public function updateOrder(Request $request, QuestionPackage $package)
    {
        $request->validate([
            'questions' => 'required|array',
            'questions.*.id' => 'required|exists:questions,id',
            'questions.*.order' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->questions as $questionData) {
                PackageQuestion::where('package_id', $package->id)
                    ->where('question_id', $questionData['id'])
                    ->update(['order_number' => $questionData['order']]);
            }
            DB::commit();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
