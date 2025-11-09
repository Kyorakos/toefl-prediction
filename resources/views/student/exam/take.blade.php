@extends('layouts.app')

@section('title', 'TOEFL Exam - ' . ucfirst($examSession->current_section))

@section('content')
<div class="min-h-screen bg-gray-100 dark:bg-gray-900 no-select no-context-menu" id="exam-container">
    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div>
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white">
                        TOEFL Exam - {{ ucfirst($examSession->current_section) }} Section
                    </h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Question {{ $examSession->current_question }} of {{ $questions->count() }}
                    </p>
                </div>
                
                <!-- Timer -->
                <div class="flex items-center space-x-4">
                    <div id="timer" class="text-lg font-bold text-red-600 dark:text-red-400">
                        <span id="timer-display">{{ gmdate('H:i:s', $remainingTime) }}</span>
                    </div>
                    
                    <!-- Tab Switch Warning -->
                    <div id="tab-warning" class="text-sm text-orange-600 dark:text-orange-400" style="display: none;">
                        Warning: {{ $examSession->tab_switch_count }}/3 tab switches
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Progress Bar -->
    <div class="bg-gray-200 dark:bg-gray-700 h-2">
        <div class="bg-blue-600 h-2 transition-all duration-300" style="width: {{ ($examSession->current_question / $questions->count()) * 100 }}%"></div>
    </div>
    
    <!-- Question Content -->
    <div class="max-w-4xl mx-auto p-6">
        @if($currentQuestion)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 mb-6">
                <!-- Audio Player (for listening section) -->
                @if($currentQuestion->hasAudio())
                    <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900 rounded-lg">
                        <div class="flex items-center justify-between">
                            <span class="text-blue-800 dark:text-blue-200 font-medium">Audio Question</span>
                            <audio controls preload="metadata" class="audio-player" data-question-id="{{ $currentQuestion->id }}">
                                <source src="{{ $currentQuestion->getAudioUrl() }}" type="audio/mpeg">
                                Your browser does not support the audio element.
                            </audio>
                        </div>
                        <p class="text-sm text-blue-600 dark:text-blue-400 mt-2">
                            ⚠️ Audio can only be played once. Listen carefully!
                        </p>
                    </div>
                @endif
                
                <!-- Reading Passage -->
                @if($currentQuestion->hasPassage())
                    <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Reading Passage</h3>
                        <div class="prose dark:prose-invert max-w-none">
                            {!! nl2br(e($currentQuestion->passage)) !!}
                        </div>
                    </div>
                @endif
                
                <!-- Question -->
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                        Question {{ $examSession->current_question }}
                    </h3>
                    <div class="prose dark:prose-invert max-w-none">
                        {!! nl2br(e($currentQuestion->question)) !!}
                    </div>
                </div>
                
                <!-- Answer Options -->
                <div class="space-y-3">
                    @foreach(['a', 'b', 'c', 'd'] as $option)
                        <label class="flex items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer transition-colors">
                            <input type="radio" 
                                   name="answer" 
                                   value="{{ $option }}" 
                                   class="mr-3 text-blue-600 dark:text-blue-400"
                                   {{ isset($answers[$currentQuestion->id]) && $answers[$currentQuestion->id] === $option ? 'checked' : '' }}>
                            <span class="text-gray-900 dark:text-white">
                                <strong>{{ strtoupper($option) }}.</strong> {{ $currentQuestion->getOptionsArray()[$option] }}
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>
            
            <!-- Navigation -->
            <div class="flex justify-between items-center">
                <button id="prev-btn" 
                        class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed"
                        {{ $examSession->current_question <= 1 ? 'disabled' : '' }}>
                    ← Previous
                </button>
                
                <div class="flex space-x-2">
                    <!-- Question Grid -->
                    <div class="flex flex-wrap gap-2">
                        @foreach($questions as $index => $question)
                            <button class="w-8 h-8 text-xs rounded {{ $index + 1 === $examSession->current_question ? 'bg-blue-600 text-white' : (isset($answers[$question->id]) ? 'bg-green-200 text-green-800' : 'bg-gray-200 text-gray-600') }}"
                                    onclick="goToQuestion({{ $index + 1 }})">
                                {{ $index + 1 }}
                            </button>
                        @endforeach
                    </div>
                </div>
                
                @if($examSession->current_question < $questions->count())
                    <button id="next-btn" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Next →
                    </button>
                @else
                    <button id="next-section-btn" 
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        @if($examSession->current_section === 'reading')
                            Submit Exam
                        @else
                            Next Section →
                        @endif
                    </button>
                @endif
            </div>
        @endif
    </div>
</div>

<!-- Anti-cheat and Timer Scripts -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const examSessionId = {{ $examSession->id }};
    let remainingTime = {{ $remainingTime }};
    let timerInterval;
    let autoSaveInterval;
    let tabSwitchCount = {{ $examSession->tab_switch_count }};
    let audioPlayedQuestions = JSON.parse(localStorage.getItem('audioPlayedQuestions') || '[]');
    
    // Anti-cheat measures
    disableRightClick();
    disableTextSelection();
    disableKeyboardShortcuts();
    detectTabSwitch();
    
    // Initialize timer
    startTimer();
    
    // Auto-save answers
    startAutoSave();
    
    // Audio restrictions
    handleAudioRestrictions();
    
    // Navigation handlers
    setupNavigation();
    
    function disableRightClick() {
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
            return false;
        });
    }
    
    function disableTextSelection() {
        document.onselectstart = function() {
            return false;
        };
        document.onmousedown = function() {
            return false;
        };
    }
    
    function disableKeyboardShortcuts() {
        document.addEventListener('keydown', function(e) {
            // Disable F12, Ctrl+Shift+I, Ctrl+Shift+J, Ctrl+U, Ctrl+S, Ctrl+A, Ctrl+C, Ctrl+V
            if (e.keyCode === 123 || 
                (e.ctrlKey && e.shiftKey && (e.keyCode === 73 || e.keyCode === 74)) ||
                (e.ctrlKey && (e.keyCode === 85 || e.keyCode === 83 || e.keyCode === 65 || e.keyCode === 67 || e.keyCode === 86))) {
                e.preventDefault();
                return false;
            }
        });
    }
    
    function detectTabSwitch() {
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                handleTabSwitch();
            }
        });
        
        window.addEventListener('blur', function() {
            handleTabSwitch();
        });
    }
    
    function handleTabSwitch() {
        fetch(`/student/exam/${examSessionId}/tab-switch`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.auto_submit) {
                alert('Exam submitted due to multiple tab switches!');
                window.location.href = '/student/dashboard';
            } else if (data.warning) {
                tabSwitchCount = data.count;
                document.getElementById('tab-warning').style.display = 'block';
                document.getElementById('tab-warning').textContent = `Warning: ${data.count}/3 tab switches`;
                alert(`Warning: Tab switching detected! ${data.remaining} attempts remaining.`);
            }
        });
    }
    
    function startTimer() {
        updateTimerDisplay();
        
        timerInterval = setInterval(function() {
            remainingTime--;
            updateTimerDisplay();
            
            if (remainingTime <= 0) {
                clearInterval(timerInterval);
                submitSection();
            }
            
            // Sync with server every 30 seconds
            if (remainingTime % 30 === 0) {
                syncTimer();
            }
        }, 1000);
    }
    
    function updateTimerDisplay() {
        const hours = Math.floor(remainingTime / 3600);
        const minutes = Math.floor((remainingTime % 3600) / 60);
        const seconds = remainingTime % 60;
        
        const display = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        document.getElementById('timer-display').textContent = display;
        
        // Warning when less than 5 minutes
        if (remainingTime < 300) {
            document.getElementById('timer').classList.add('timer-warning');
        }
    }
    
    function syncTimer() {
        fetch(`/student/exam/${examSessionId}/sync-timer`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.time_up) {
                clearInterval(timerInterval);
                submitSection();
            } else {
                remainingTime = data.remaining;
            }
        });
    }
    
    function startAutoSave() {
        autoSaveInterval = setInterval(function() {
            saveCurrentAnswer();
        }, 30000); // Save every 30 seconds
    }
    
    function saveCurrentAnswer() {
        const selectedAnswer = document.querySelector('input[name="answer"]:checked');
        if (selectedAnswer) {
            const questionId = {{ $currentQuestion->id }};
            
            fetch(`/student/exam/${examSessionId}/save-answer`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    question_id: questionId,
                    answer: selectedAnswer.value
                })
            });
        }
    }
    
    function handleAudioRestrictions() {
        const audioPlayer = document.querySelector('.audio-player');
        if (audioPlayer) {
            const questionId = audioPlayer.dataset.questionId;
            
            if (audioPlayedQuestions.includes(questionId)) {
                audioPlayer.style.display = 'none';
                audioPlayer.parentElement.innerHTML += '<p class="text-red-600 dark:text-red-400">Audio has already been played for this question.</p>';
            } else {
                audioPlayer.addEventListener('ended', function() {
                    audioPlayedQuestions.push(questionId);
                    localStorage.setItem('audioPlayedQuestions', JSON.stringify(audioPlayedQuestions));
                    
                    // Hide audio player after playing
                    audioPlayer.style.display = 'none';
                    audioPlayer.parentElement.innerHTML += '<p class="text-green-600 dark:text-green-400">Audio played. You cannot replay this audio.</p>';
                });
            }
        }
    }
    
    function setupNavigation() {
        document.getElementById('prev-btn')?.addEventListener('click', function() {
            navigate('prev');
        });
        
        document.getElementById('next-btn')?.addEventListener('click', function() {
            navigate('next');
        });
        
        document.getElementById('next-section-btn')?.addEventListener('click', function() {
            if (confirm('Are you sure you want to proceed to the next section? You cannot go back.')) {
                saveCurrentAnswer();
                if ({{ $examSession->current_section === 'reading' ? 'true' : 'false' }}) {
                    submitExam();
                } else {
                    nextSection();
                }
            }
        });
        
        // Save answer when option is selected
        document.querySelectorAll('input[name="answer"]').forEach(function(radio) {
            radio.addEventListener('change', function() {
                saveCurrentAnswer();
            });
        });
    }
    
    function navigate(direction) {
        saveCurrentAnswer();
        
        fetch(`/student/exam/${examSessionId}/navigate`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                direction: direction
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
    
    function goToQuestion(questionNumber) {
        // Implementation for jumping to specific question
        const currentQuestion = {{ $examSession->current_question }};
        const direction = questionNumber > currentQuestion ? 'next' : 'prev';
        const steps = Math.abs(questionNumber - currentQuestion);
        
        // Navigate step by step
        for (let i = 0; i < steps; i++) {
            setTimeout(() => navigate(direction), i * 200);
        }
    }
    
    function nextSection() {
        window.location.href = `/student/exam/${examSessionId}/next-section`;
    }
    
    function submitSection() {
        saveCurrentAnswer();
        nextSection();
    }
    
    function submitExam() {
        window.location.href = `/student/exam/${examSessionId}/submit`;
    }
    
    // Cleanup intervals when page unloads
    window.addEventListener('beforeunload', function() {
        clearInterval(timerInterval);
        clearInterval(autoSaveInterval);
        saveCurrentAnswer();
    });
});
</script>
@endsection
