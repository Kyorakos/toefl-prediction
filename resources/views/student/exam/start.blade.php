@extends('layouts.app')

@section('title', 'Start Exam')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-gray-900 dark:to-gray-800 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 shadow-xl rounded-lg overflow-hidden">
            <div class="px-6 py-8">
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                        TOEFL Prediction Test
                    </h1>
                    <p class="text-lg text-gray-600 dark:text-gray-400">
                        {{ $batch->name }}
                    </p>
                </div>
                
                <!-- Package Information -->
                <div class="bg-blue-50 dark:bg-blue-900 rounded-lg p-6 mb-8">
                    <h2 class="text-xl font-semibold text-blue-900 dark:text-blue-100 mb-4">
                        Test Structure
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                {{ $batch->questionPackage->listening_questions }}
                            </div>
                            <div class="text-sm text-blue-800 dark:text-blue-200">
                                Listening Questions
                            </div>
                            <div class="text-xs text-blue-600 dark:text-blue-400">
                                {{ $batch->questionPackage->listening_time }} minutes
                            </div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                                {{ $batch->questionPackage->structure_questions }}
                            </div>
                            <div class="text-sm text-green-800 dark:text-green-200">
                                Structure Questions
                            </div>
                            <div class="text-xs text-green-600 dark:text-green-400">
                                {{ $batch->questionPackage->structure_time }} minutes
                            </div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                                {{ $batch->questionPackage->reading_questions }}
                            </div>
                            <div class="text-sm text-purple-800 dark:text-purple-200">
                                Reading Questions
                            </div>
                            <div class="text-xs text-purple-600 dark:text-purple-400">
                                {{ $batch->questionPackage->reading_time }} minutes
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Instructions -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                        Test Instructions
                    </h2>
                    <div class="space-y-4 text-gray-700 dark:text-gray-300">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-6 h-6 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center mr-3">
                                <span class="text-red-600 dark:text-red-400 text-sm font-bold">1</span>
                            </div>
                            <p>This is a timed test. You must complete each section within the allocated time.</p>
                        </div>
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-6 h-6 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center mr-3">
                                <span class="text-red-600 dark:text-red-400 text-sm font-bold">2</span>
                            </div>
                            <p>For listening questions, audio clips can only be played ONCE. Listen carefully.</p>
                        </div>
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-6 h-6 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center mr-3">
                                <span class="text-red-600 dark:text-red-400 text-sm font-bold">3</span>
                            </div>
                            <p>You can navigate between questions within the same section, but cannot go back to previous sections.</p>
                        </div>
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-6 h-6 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center mr-3">
                                <span class="text-red-600 dark:text-red-400 text-sm font-bold">4</span>
                            </div>
                            <p>Your answers are auto-saved every 30 seconds. The test will auto-submit when time runs out.</p>
                        </div>
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-6 h-6 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center mr-3">
                                <span class="text-red-600 dark:text-red-400 text-sm font-bold">5</span>
                            </div>
                            <p><strong>WARNING:</strong> Switching tabs or leaving the exam page will be detected. After 3 violations, your exam will be automatically submitted.</p>
                        </div>
                    </div>
                </div>
                
                <!-- Integrity Agreement -->
                <div class="bg-yellow-50 dark:bg-yellow-900 border border-yellow-200 dark:border-yellow-700 rounded-lg p-6 mb-8">
                    <h3 class="text-lg font-semibold text-yellow-800 dark:text-yellow-200 mb-3">
                        Academic Integrity Agreement
                    </h3>
                    <div class="space-y-2 text-sm text-yellow-700 dark:text-yellow-300">
                        <p>By starting this exam, you agree to:</p>
                        <ul class="list-disc list-inside space-y-1 ml-4">
                            <li>Complete the exam independently without external help</li>
                            <li>Not use any unauthorized materials or resources</li>
                            <li>Not communicate with others during the exam</li>
                            <li>Not attempt to access other websites or applications</li>
                            <li>Accept the consequences of any integrity violations</li>
                        </ul>
                    </div>
                    
                    <div class="mt-4">
                        <label class="flex items-center">
                            <input type="checkbox" id="integrity-agreement" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-yellow-700 dark:text-yellow-300">
                                I have read and agree to the academic integrity requirements
                            </span>
                        </label>
                    </div>
                </div>
                
                <!-- Start Button -->
                <div class="text-center">
                    <form method="POST" action="{{ route('student.exam.begin') }}">
                        @csrf
                        <input type="hidden" name="session_id" value="{{ $existingSession->id }}">
                        <button type="submit" id="start-exam-btn" disabled
                                class="px-8 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                            Start Exam
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const agreementCheckbox = document.getElementById('integrity-agreement');
    const startButton = document.getElementById('start-exam-btn');
    
    agreementCheckbox.addEventListener('change', function() {
        startButton.disabled = !this.checked;
    });
});
</script>
@endsection
