@extends('layouts.app')

@section('title', 'Exam Completed')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-green-50 to-blue-100 dark:from-gray-900 dark:to-gray-800 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 shadow-xl rounded-lg overflow-hidden">
            <div class="px-6 py-8">
                <div class="text-center mb-8">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 dark:bg-green-900 mb-4">
                        <svg class="h-8 w-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                        Exam Completed Successfully!
                    </h1>
                    <p class="text-lg text-gray-600 dark:text-gray-400">
                        Thank you for taking the TOEFL Prediction Test
                    </p>
                </div>
                
                <!-- Results Summary -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                        Your Results
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="text-center">
                            <div class="text-4xl font-bold text-green-600 dark:text-green-400 mb-2">
                                {{ $examSession->total_score }}
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                Total Score
                            </div>
                        </div>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Listening:</span>
                                <span class="font-semibold text-gray-900 dark:text-white">{{ $examSession->listening_score }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Structure:</span>
                                <span class="font-semibold text-gray-900 dark:text-white">{{ $examSession->structure_score }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Reading:</span>
                                <span class="font-semibold text-gray-900 dark:text-white">{{ $examSession->reading_score }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Exam Details -->
                <div class="bg-blue-50 dark:bg-blue-900 rounded-lg p-6 mb-8">
                    <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-4">
                        Exam Details
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <strong class="text-blue-800 dark:text-blue-200">Batch:</strong>
                            <span class="text-blue-700 dark:text-blue-300">{{ $examSession->batch->name }}</span>
                        </div>
                        <div>
                            <strong class="text-blue-800 dark:text-blue-200">Started:</strong>
                            <span class="text-blue-700 dark:text-blue-300">{{ $examSession->started_at->format('Y-m-d H:i') }}</span>
                        </div>
                        <div>
                            <strong class="text-blue-800 dark:text-blue-200">Completed:</strong>
                            <span class="text-blue-700 dark:text-blue-300">{{ $examSession->completed_at->format('Y-m-d H:i') }}</span>
                        </div>
                        <div>
                            <strong class="text-blue-800 dark:text-blue-200">Duration:</strong>
                            <span class="text-blue-700 dark:text-blue-300">{{ $examSession->completed_at->diffForHumans($examSession->started_at, true) }}</span>
                        </div>
                    </div>
                </div>
                
                <!-- Next Steps -->
                <div class="bg-yellow-50 dark:bg-yellow-900 border border-yellow-200 dark:border-yellow-700 rounded-lg p-6 mb-8">
                    <h3 class="text-lg font-semibold text-yellow-800 dark:text-yellow-200 mb-3">
                        What's Next?
                    </h3>
                    <div class="space-y-2 text-sm text-yellow-700 dark:text-yellow-300">
                        <p>• Your detailed results will be reviewed by our administrators</p>
                        <p>• You will receive feedback and analysis via your registered communication channel</p>
                        <p>• Additional study recommendations may be provided based on your performance</p>
                        <p>• Keep an eye on announcements for future test sessions</p>
                    </div>
                </div>
                
                <!-- Action Button -->
                <div class="text-center">
                    <a href="{{ route('student.dashboard') }}" 
                       class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Return to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
