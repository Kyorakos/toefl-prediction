import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Global functions for exam functionality
window.examUtils = {
    // Timer functions
    formatTime: function(seconds) {
        const hours = Math.floor(seconds / 3600);
        const minutes = Math.floor((seconds % 3600) / 60);
        const secs = seconds % 60;
        return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
    },

    // Audio restrictions
    restrictAudioPlayback: function(audioElement, questionId) {
        const playedQuestions = JSON.parse(localStorage.getItem('audioPlayedQuestions') || '[]');
        
        if (playedQuestions.includes(questionId)) {
            audioElement.style.display = 'none';
            return false;
        }
        
        audioElement.addEventListener('ended', function() {
            playedQuestions.push(questionId);
            localStorage.setItem('audioPlayedQuestions', JSON.stringify(playedQuestions));
            audioElement.style.display = 'none';
            
            // Show message
            const message = document.createElement('p');
            message.className = 'text-green-600 dark:text-green-400 text-sm mt-2';
            message.textContent = 'Audio played. You cannot replay this audio.';
            audioElement.parentElement.appendChild(message);
        });
        
        return true;
    },

    // Anti-cheat measures
    preventRightClick: function() {
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
            return false;
        });
    },

    preventTextSelection: function() {
        document.body.classList.add('no-select');
    },

    preventKeyboardShortcuts: function() {
        document.addEventListener('keydown', function(e) {
            // Prevent F12, Ctrl+Shift+I, Ctrl+Shift+J, Ctrl+U, etc.
            if (e.keyCode === 123 || 
                (e.ctrlKey && e.shiftKey && (e.keyCode === 73 || e.keyCode === 74)) ||
                (e.ctrlKey && (e.keyCode === 85 || e.keyCode === 83 || e.keyCode === 65 || e.keyCode === 67 || e.keyCode === 86))) {
                e.preventDefault();
                return false;
            }
        });
    },

    // Tab switch detection
    detectTabSwitch: function(callback) {
        let isTabActive = true;
        
        document.addEventListener('visibilitychange', function() {
            if (document.hidden && isTabActive) {
                isTabActive = false;
                callback();
            } else if (!document.hidden) {
                isTabActive = true;
            }
        });
        
        window.addEventListener('blur', function() {
            if (isTabActive) {
                isTabActive = false;
                callback();
            }
        });
        
        window.addEventListener('focus', function() {
            isTabActive = true;
        });
    },

    // AJAX helpers
    makeRequest: function(url, options = {}) {
        const defaultOptions = {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        };
        
        const mergedOptions = { ...defaultOptions, ...options };
        
        return fetch(url, mergedOptions)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .catch(error => {
                console.error('Request failed:', error);
                throw error;
            });
    },

    // Local storage helpers
    saveToStorage: function(key, value) {
        try {
            localStorage.setItem(key, JSON.stringify(value));
        } catch (e) {
            console.error('Failed to save to localStorage:', e);
        }
    },

    getFromStorage: function(key, defaultValue = null) {
        try {
            const item = localStorage.getItem(key);
            return item ? JSON.parse(item) : defaultValue;
        } catch (e) {
            console.error('Failed to get from localStorage:', e);
            return defaultValue;
        }
    },

    // Notification helpers
    showNotification: function(message, type = 'info', duration = 3000) {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 px-4 py-2 rounded-md shadow-lg ${this.getNotificationClasses(type)}`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, duration);
    },

    getNotificationClasses: function(type) {
        switch (type) {
            case 'success':
                return 'bg-green-500 text-white';
            case 'error':
                return 'bg-red-500 text-white';
            case 'warning':
                return 'bg-yellow-500 text-white';
            default:
                return 'bg-blue-500 text-white';
        }
    },

    // Form validation
    validateForm: function(formElement) {
        const requiredFields = formElement.querySelectorAll('[required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                this.showFieldError(field, 'This field is required');
                isValid = false;
            } else {
                this.clearFieldError(field);
            }
        });
        
        return isValid;
    },

    showFieldError: function(field, message) {
        this.clearFieldError(field);
        
        const errorElement = document.createElement('div');
        errorElement.className = 'text-red-500 text-sm mt-1';
        errorElement.textContent = message;
        errorElement.setAttribute('data-error-for', field.name);
        
        field.parentElement.appendChild(errorElement);
        field.classList.add('border-red-500');
    },

    clearFieldError: function(field) {
        const errorElement = field.parentElement.querySelector(`[data-error-for="${field.name}"]`);
        if (errorElement) {
            errorElement.remove();
        }
        field.classList.remove('border-red-500');
    }
};

// Initialize common functionality
document.addEventListener('DOMContentLoaded', function() {
    // Initialize dark mode
    if (localStorage.getItem('darkMode') === 'true') {
        document.documentElement.classList.add('dark');
    }
    
    // Initialize tooltips
    const tooltips = document.querySelectorAll('[data-tooltip]');
    tooltips.forEach(tooltip => {
        tooltip.addEventListener('mouseenter', function() {
            const tooltipText = this.getAttribute('data-tooltip');
            const tooltipElement = document.createElement('div');
            tooltipElement.className = 'absolute z-50 px-2 py-1 text-sm bg-gray-800 text-white rounded shadow-lg';
            tooltipElement.textContent = tooltipText;
            tooltipElement.style.top = this.offsetTop + this.offsetHeight + 5 + 'px';
            tooltipElement.style.left = this.offsetLeft + 'px';
            tooltipElement.setAttribute('data-tooltip-element', '');
            
            this.parentElement.appendChild(tooltipElement);
        });
        
        tooltip.addEventListener('mouseleave', function() {
            const tooltipElement = this.parentElement.querySelector('[data-tooltip-element]');
            if (tooltipElement) {
                tooltipElement.remove();
            }
        });
    });
    
    // Initialize form validation
    const forms = document.querySelectorAll('form[data-validate]');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!window.examUtils.validateForm(this)) {
                e.preventDefault();
            }
        });
    });
});
