<?php
// Check authentication
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: /Projects/study-tools-website/public/login');
    exit();
}

// Set page variables
$page_title = 'Pomodoro Timer';
$show_breadcrumb = true;
$page_css = ['/assets/css/modules/pomodoro.css'];
$page_js = ['/assets/js/modules/pomodoro.js'];

ob_start();
?>

<div class="pomodoro-container">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-text mb-2">🍅 Pomodoro Timer</h1>
        <p class="text-text-secondary">Focus for 25 minutes, break for 5. Repeat and be productive!</p>
    </div>

    <!-- Stats Section - THÊM totalHours -->
    <div class="stats-grid mb-8">
        <div class="stat-card">
            <div class="stat-value" id="totalSessions">0</div>
            <div class="stat-label">Total Sessions</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" id="totalMinutes">0</div>
            <div class="stat-label">Minutes Focused</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" id="totalHours">0</div>
            <div class="stat-label">Hours Focused</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" id="todaySessions">0</div>
            <div class="stat-label">Today's Sessions</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" id="avgDuration">0 min</div>
            <div class="stat-label">Avg. Duration</div>
        </div>
    </div>

    <!-- Main Timer Section -->
    <div class="timer-container mb-8">
        <!-- Timer Display -->
        <div class="timer-display">
            <div class="timer-time" id="timerTime">25:00</div>
            <div class="timer-label" id="timerLabel">💼 Work Session</div>
            <div class="timer-progress">
                <div class="progress">
                    <div class="progress-bar" id="timerProgress"></div>
                </div>
            </div>
        </div>
        
        <!-- Study Topic Input -->
        <div class="study-topic-group" id="studyTopicGroup">
            <div class="study-topic-input">
                <input 
                    type="text" 
                    class="form-control" 
                    id="studyTopic" 
                    placeholder="What are you working on? (Optional)"
                    maxlength="255"
                >
                <p class="text-xs text-text-secondary mt-1 text-center">
                    Enter a topic to track what you're focusing on
                </p>
            </div>
        </div>
        
        <!-- Timer Controls -->
        <div class="timer-controls">
            <button class="btn btn-primary" id="startBtn">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" class="mr-2">
                    <path d="M8 5V19L19 12L8 5Z" fill="currentColor"/>
                </svg>
                Start
            </button>
            <button class="btn btn-secondary" id="pauseBtn" disabled>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" class="mr-2">
                    <path d="M6 19H10V5H6V19ZM14 5V19H18V5H14Z" fill="currentColor"/>
                </svg>
                Pause
            </button>
            <button class="btn btn-warning" id="endBtn" disabled>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" class="mr-2">
                    <path d="M6 6H18V18H6V6Z" fill="currentColor"/>
                </svg>
                End
            </button>
            <button class="btn btn-info" id="switchBtn" disabled>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" class="mr-2">
                    <path d="M12 4V20M20 12H4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Switch
            </button>
        </div>
        
        <!-- Session Info -->
        <div class="session-info">
            <div class="session-info-grid">
                <div class="session-info-item">
                    <div class="session-info-label">Work Duration</div>
                    <div class="session-info-value" id="workDuration">25 min</div>
                </div>
                <div class="session-info-item">
                    <div class="session-info-label">Break Duration</div>
                    <div class="session-info-value" id="breakDuration">5 min</div>
                </div>
            </div>
            <div class="sessions-count">
                <div class="session-info-label">Sessions Completed Today</div>
                <div class="sessions-count-value" id="sessionsCompleted">0</div>
            </div>
        </div>
        
        <!-- Settings Button -->
        <div class="text-center mt-6">
            <button class="btn btn-outline" id="settingsBtn">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" class="mr-2">
                    <path d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M19.4 15C19.2663 15.3054 19.1995 15.639 19.2 16C19.2 16.361 19.2663 16.6946 19.4 17L21.4 21C21.5556 21.3466 21.5838 21.7386 21.4792 22.1068C21.3746 22.475 21.1438 22.7949 20.8282 23.0112C20.5125 23.2275 20.1328 23.3264 19.7482 23.2908C19.3636 23.2552 19.0002 23.0873 18.7218 22.816L16.8982 20.928C16.2946 20.2852 15.6677 19.6664 15.0182 19.073C14.9418 19.024 14.8655 18.975 14.7891 18.926L14.5218 19.189C13.9977 19.713 13.3655 20.1164 12.6727 20.3718C11.9799 20.6273 11.2445 20.7286 10.5127 20.6688C9.78097 20.609 9.07125 20.3896 8.43543 20.0267C7.7996 19.6638 7.25388 19.1667 6.83818 18.572L4.6 21C4.3216 21.2713 3.95821 21.4392 3.57359 21.4748C3.18897 21.5104 2.80925 21.4115 2.49361 21.1952C2.17796 20.9789 1.94721 20.659 1.84261 20.2908C1.73801 19.9226 1.76618 19.5306 1.92182 19.184L3.92182 15.184C4.05549 14.8786 4.1223 14.545 4.12182 14.184C4.12182 13.823 4.05549 13.4894 3.92182 13.184L1.92182 9.184C1.76618 8.83738 1.73801 8.44542 1.84261 8.07721C1.94721 7.709 2.17796 7.38907 2.49361 7.17278C2.80925 6.95649 3.18897 6.85757 3.57359 6.89218C3.95821 6.92679 4.3216 7.09466 4.6 7.366L6.83818 9.794C7.25388 10.3887 7.7996 10.8858 8.43543 11.2487C9.07125 11.6116 9.78097 11.831 10.5127 11.8908C11.2445 11.9506 11.9799 11.8493 12.6727 11.5938C13.3655 11.3384 13.9977 10.935 14.5218 10.411L14.7891 10.148C14.8655 10.099 14.9418 10.05 15.0182 10.001C15.6677 9.40764 16.2946 8.78884 16.8982 8.146L18.7218 6.258C19.0002 5.98666 19.3636 5.81879 19.7482 5.78418C20.1328 5.74957 20.5125 5.85049 20.8282 6.06678C21.1438 6.28307 21.3746 6.603 21.4792 6.97121C21.5838 7.33942 21.5556 7.73138 21.4 8.078L19.4 12.078C19.2663 12.3834 19.1995 12.717 19.2 13.078C19.2 13.439 19.2663 13.7726 19.4 14.078L21.4 18.078C21.5556 18.4246 21.5838 18.8166 21.4792 19.1848C21.3746 19.553 21.1438 19.8729 20.8282 20.0892C20.5125 20.3055 20.1328 20.4044 19.7482 20.3698C19.3636 20.3352 18.9992 20.1693 18.7218 19.898L16.8982 18.01C16.2946 17.3672 15.6677 16.7484 15.0182 16.155C14.9418 16.106 14.8655 16.057 14.7891 16.008L14.5218 16.271C13.9977 16.795 13.3655 17.1984 12.6727 17.4538C11.9799 17.7093 11.2445 17.8106 10.5127 17.7508C9.78097 17.691 9.07125 17.4716 8.43543 17.1087C7.7996 16.7458 7.25388 16.2487 6.83818 15.654L4.6 18.082" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Settings
            </button>
        </div>
    </div>

    <!-- Recent Sessions -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">📊 Recent Sessions</h2>
            <div class="card-actions">
                <button class="btn btn-outline" id="refreshHistoryBtn">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" class="mr-2">
                        <path d="M4 4V9H4.58152M19.9381 11C19.446 7.05369 16.0796 4 12 4C8.64262 4 5.76829 6.06817 4.58152 9M4.58152 9H9M20 20V15H19.4185M19.4185 15C18.2317 17.9318 15.3574 20 12 20C7.92038 20 4.55399 16.9463 4.06189 13M19.4185 15H15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Refresh
                </button>
            </div>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="history-table">
                    <thead>
                        <tr>
                            <th>Topic</th>
                            <th>Duration</th>
                            <th>Status</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody id="historyBody">
                        <tr>
                            <td colspan="4" class="p-8 text-center text-text-secondary">
                                <div class="empty-state">
                                    <div class="empty-state-icon">📊</div>
                                    <div class="empty-state-text">No sessions yet</div>
                                    <div class="empty-state-subtext">Start your first Pomodoro session!</div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Settings Modal -->
<div class="modal-overlay" id="settingsModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                    <path d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M19.4 15C19.2663 15.3054 19.1995 15.639 19.2 16C19.2 16.361 19.2663 16.6946 19.4 17L21.4 21C21.5556 21.3466 21.5838 21.7386 21.4792 22.1068C21.3746 22.475 21.1438 22.7949 20.8282 23.0112C20.5125 23.2275 20.1328 23.3264 19.7482 23.2908C19.3636 23.2552 19.0002 23.0873 18.7218 22.816L16.8982 20.928C16.2946 20.2852 15.6677 19.6664 15.0182 19.073C14.9418 19.024 14.8655 18.975 14.7891 18.926L14.5218 19.189C13.9977 19.713 13.3655 20.1164 12.6727 20.3718C11.9799 20.6273 11.2445 20.7286 10.5127 20.6688C9.78097 20.609 9.07125 20.3896 8.43543 20.0267C7.7996 19.6638 7.25388 19.1667 6.83818 18.572L4.6 21C4.3216 21.2713 3.95821 21.4392 3.57359 21.4748C3.18897 21.5104 2.80925 21.4115 2.49361 21.1952C2.17796 20.9789 1.94721 20.659 1.84261 20.2908C1.73801 19.9226 1.76618 19.5306 1.92182 19.184L3.92182 15.184C4.05549 14.8786 4.1223 14.545 4.12182 14.184C4.12182 13.823 4.05549 13.4894 3.92182 13.184L1.92182 9.184C1.76618 8.83738 1.73801 8.44542 1.84261 8.07721C1.94721 7.709 2.17796 7.38907 2.49361 7.17278C2.80925 6.95649 3.18897 6.85757 3.57359 6.89218C3.95821 6.92679 4.3216 7.09466 4.6 7.366L6.83818 9.794C7.25388 10.3887 7.7996 10.8858 8.43543 11.2487C9.07125 11.6116 9.78097 11.831 10.5127 11.8908C11.2445 11.9506 11.9799 11.8493 12.6727 11.5938C13.3655 11.3384 13.9977 10.935 14.5218 10.411L14.7891 10.148C14.8655 10.099 14.9418 10.05 15.0182 10.001C15.6677 9.40764 16.2946 8.78884 16.8982 8.146L18.7218 6.258C19.0002 5.98666 19.3636 5.81879 19.7482 5.78418C20.1328 5.74957 20.5125 5.85049 20.8282 6.06678C21.1438 6.28307 21.3746 6.603 21.4792 6.97121C21.5838 7.33942 21.5556 7.73138 21.4 8.078L19.4 12.078C19.2663 12.3834 19.1995 12.717 19.2 13.078C19.2 13.439 19.2663 13.7726 19.4 14.078L21.4 18.078C21.5556 18.4246 21.5838 18.8166 21.4792 19.1848C21.3746 19.553 21.1438 19.8729 20.8282 20.0892C20.5125 20.3055 20.1328 20.4044 19.7482 20.3698C19.3636 20.3352 18.9992 20.1693 18.7218 19.898L16.8982 18.01C16.2946 17.3672 15.6677 16.7484 15.0182 16.155C14.9418 16.106 14.8655 16.057 14.7891 16.008L14.5218 16.271C13.9977 16.795 13.3655 17.1984 12.6727 17.4538C11.9799 17.7093 11.2445 17.8106 10.5127 17.7508C9.78097 17.691 9.07125 17.4716 8.43543 17.1087C7.7996 16.7458 7.25388 16.2487 6.83818 15.654L4.6 18.082" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Pomodoro Settings
            </h2>
            <button class="modal-close" id="closeSettingsBtn">&times;</button>
        </div>
        <div class="modal-body">
            <form id="settingsForm" class="settings-form">
                <div class="form-group">
                    <label class="form-label" for="settingsWorkDuration">Work Duration (minutes)</label>
                    <input 
                        type="number" 
                        class="form-control" 
                        id="settingsWorkDuration" 
                        min="1" 
                        max="60" 
                        value="25"
                        required
                    >
                    <p class="text-xs text-text-secondary mt-1">Recommended: 25 minutes</p>
                </div>
                <div class="form-group">
                    <label class="form-label" for="settingsBreakDuration">Break Duration (minutes)</label>
                    <input 
                        type="number" 
                        class="form-control" 
                        id="settingsBreakDuration" 
                        min="1" 
                        max="30" 
                        value="5"
                        required
                    >
                    <p class="text-xs text-text-secondary mt-1">Recommended: 5 minutes</p>
                </div>
                <div class="form-group">
                    <div class="form-checkbox-group">
                        <input 
                            type="checkbox" 
                            class="form-checkbox" 
                            id="autoStart"
                        >
                        <label for="autoStart" class="cursor-pointer">
                            <div class="font-medium text-text">Auto-start next session</div>
                            <div class="text-sm text-text-secondary">Automatically switch to break after work session</div>
                        </label>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline" id="cancelSettingsBtn">Cancel</button>
            <button type="button" class="btn btn-primary" id="saveSettingsBtn">Save Settings</button>
        </div>
    </div>
</div>

<!-- Notification Sound -->
<audio id="timerSound" preload="auto">
    <source src="/assets/sounds/notification.mp3" type="audio/mpeg">
    Your browser does not support the audio element.
</audio>

<!-- Fallback for no audio support -->
<div id="audioFallback" style="display: none;"></div>

<script>
// Fallback notification if audio doesn't work
function playFallbackNotification() {
    const fallback = document.getElementById('audioFallback');
    fallback.innerHTML = '<div style="position: fixed; top: 20px; right: 20px; background: #4a6cf7; color: white; padding: 1rem; border-radius: 8px; z-index: 10000; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">⏰ Timer Complete!</div>';
    setTimeout(() => {
        fallback.innerHTML = '';
    }, 3000);
}

// Check audio support
document.addEventListener('DOMContentLoaded', function() {
    const audio = document.getElementById('timerSound');
    if (!audio.canPlayType('audio/mpeg')) {
        console.log('Audio not supported, using fallback notifications');
        // Override playNotificationSound in PomodoroApp
        if (window.pomodoroApp) {
            window.pomodoroApp.playNotificationSound = playFallbackNotification;
        }
    }
});
</script>
