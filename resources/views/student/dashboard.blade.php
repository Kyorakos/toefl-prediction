@extends('layouts.app')

@section('title', 'Student Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Welcome, {{ auth()->user()->name }}!</h1>
        <p class="mt-2 text-gray-600 dark:text-gray-400">Your TOEFL Prediction Test Dashboard</p>
    </div>
    
    <!-- Server Time -->
    <div class="bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg p-4 mb-6">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="text-blue-800 dark:text-blue-200">
                Server Time (Asia/Jakarta): <span id="server-time" class="font-semibold">{{ $serverTime->format('Y-m-d H:i:s') }}</span>
            </span>
        </div>
    </div>
    
    @if($batch)
        <!-- Batch Information -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Your Batch Information</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="font-medium text-gray-900 dark:text-white">{{ $batch->name }}</h4>
                        <p class="text-gray-500 dark:text-gray-400 mt-1">{{ $batch->description }}</p>
                        <div class="mt-4 space-y-2">
                            <div class="flex items-center text-sm">
                                <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a1 1 0 011-1h6a1 1 0 011 1v4m2 2V7a1 1 0 00-1-1H7a1 1 0 00-1 1v2m14 0v10a1 1 0 01-1 1H5a1 1 0 01-1-1V9m14 0H5"></path>
                                </svg>
                                <span class="text-gray-600 dark:text-gray-400">Start: {{ $batch->start_time->format('Y-m-d H:i') }}</span>
                            </div>
                            <div class="flex items-center text-sm">
                                <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a1 1 0 011-1h6a1 1 0 011 1v4m2 2V7a1 1 0 00-1-1H7a1 1 0 00-1 1v2m14 0v10a1 1 0 01-1 1H5a1 1 0 01-1-1V9m14 0H5"></path>
                                </svg>
                                <span class="text-gray-600 dark:text-gray-400">End: {{ $batch->end_time->format('Y-m-d H:i') }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <h5 class="font-medium text-gray-900 dark:text-white mb-2">Test Structure</h5>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Listening:</span>
                                <span class="text-gray-900 dark:text-white">{{ $batch->questionPackage->listening_questions }} questions ({{ $batch->questionPackage->listening_time }} min)</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Structure:</span>
                                <span class="text-gray-900 dark:text-white">{{ $batch->questionPackage->structure_questions }} questions ({{ $batch->questionPackage->structure_time }} min)</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Reading:</span>
                                <span class="text-gray-900 dark:text-white">{{ $batch->questionPackage->reading_questions }} questions ({{ $batch->questionPackage->reading_time }} min)</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Exam Status -->
                <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    @if($currentExamSession)
                        @if($currentExamSession->status === 'not_started')
                            @if($batch->canStart())
                                <div class="text-center">
                                    <p class="text-gray-600 dark:text-gray-400 mb-4">Your exam is ready to start!</p>
                                    <a href="{{ route('student.exam.start') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                                        Start Exam
                                    </a>
                                </div>
                            @else
                                <div class="text-center">
                                    <p class="text-gray-600 dark:text-gray-400">
                                        Exam will be available 5 minutes before start time
                                    </p>
                                </div>
                            @endif
                        @elseif($currentExamSession->status === 'in_progress')
                            <div class="text-center">
                                <p class="text-orange-600 dark:text-orange-400 mb-4">You have an exam in progress</p>
                                <a href="{{ route('student.exam.take', $currentExamSession->id) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700">
                                    Continue Exam
                                </a>
                            </div>
                        @endif
                    @else
                        @if($batch->canStart())
                            <div class="text-center">
                                <p class="text-gray-600 dark:text-gray-400 mb-4">Ready to start your exam?</p>
                                <a href="{{ route('student.exam.start') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                                    Start Exam
                                </a>
                            </div>
                        @else
                            <div class="text-center">
                                <p class="text-gray-600 dark:text-gray-400">
                                    Exam will be available 5 minutes before start time
                                </p>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    @else
        <div class="bg-yellow-50 dark:bg-yellow-900 border border-yellow-200 dark:border-yellow-700 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-yellow-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <span class="text-yellow-800 dark:text-yellow-200">
                    No active batch found. Please contact your administrator.
                </span>
            </div>
        </div>
    @endif
    
    <!-- Completed Exams -->
    @if($completedExams->count() > 0)
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Your Exam History</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Batch</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Score</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Section Scores</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Completed</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($completedExams as $exam)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $exam->batch->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-lg font-bold text-green-600">{{ $exam->total_score }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    L: {{ $exam->listening_score }}, S: {{ $exam->structure_score }}, R: {{ $exam->reading_score }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $exam->completed_at->format('Y-m-d H:i') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Update server time every second
    setInterval(function() {
        const serverTimeElement = document.getElementById('server-time');
        if (serverTimeElement) {
            const currentTime = new Date(serverTimeElement.textContent);
            currentTime.setSeconds(currentTime.getSeconds() + 1);
            serverTimeElement.textContent = currentTime.toLocaleString('sv-SE').replace(' ', ' ');
        }
    }, 1000);
});
</script>
@endsection
