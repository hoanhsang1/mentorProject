<!-- Welcome Card -->
<div class="card mb-6">
    <div class="card-header">
        <h2 class="card-title">Welcome back, <?php echo htmlspecialchars($_SESSION['fullname'] ?? $_SESSION['username']); ?>! 👋</h2>
        <span class="badge badge-primary"><?php echo htmlspecialchars($_SESSION['role'] ?? 'free'); ?> Plan</span>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="space-y-2">
            <div class="text-text-secondary text-sm">Study Time Today</div>
            <div class="text-3xl font-bold text-gradient">2h 30m</div>
            <div class="text-sm text-text-secondary">+45m from yesterday</div>
        </div>
        <div class="space-y-2">
            <div class="text-text-secondary text-sm">Completion Rate</div>
            <div class="text-3xl font-bold text-gradient">78%</div>
            <div class="progress">
                <div class="progress-bar" style="width: 78%"></div>
            </div>
        </div>
        <div class="space-y-2">
            <div class="text-text-secondary text-sm">Current Streak</div>
            <div class="text-3xl font-bold text-gradient">7 days</div>
            <div class="text-sm text-text-secondary">Keep it up! 🔥</div>
        </div>
    </div>
</div>

<!-- Quick Stats -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="card">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-text-secondary text-sm">Pending Todos</div>
                <div class="text-2xl font-bold">12</div>
            </div>
            <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" class="text-primary">
                    <path d="M9 11L12 14L22 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M21 12V19C21 19.5304 20.7893 20.0391 20.4142 20.4142C20.0391 20.7893 19.5304 21 19 21H5C4.46957 21 3.96086 20.7893 3.58579 20.4142C3.21071 20.0391 3 19.5304 3 19V5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-text-secondary text-sm">Upcoming Events</div>
                <div class="text-2xl font-bold">5</div>
            </div>
            <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" class="text-purple-600">
                    <path d="M8 7V3M16 7V3M7 11H17M5 21H19C20.1046 21 21 20.1046 21 19V7C21 5.89543 20.1046 5 19 5H5C3.89543 5 3 5.89543 3 7V19C3 20.1046 3.89543 21 5 21Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-text-secondary text-sm">Active Habits</div>
                <div class="text-2xl font-bold">8</div>
            </div>
            <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" class="text-green-600">
                    <path d="M22 11.08V12C21.9988 14.1564 21.3005 16.2547 20.0093 17.9818C18.7182 19.709 16.9033 20.9725 14.8354 21.5839C12.7674 22.1953 10.5573 22.1219 8.53447 21.3746C6.51168 20.6273 4.78465 19.2461 3.61096 17.4371C2.43727 15.628 1.87979 13.4881 2.02168 11.3363C2.16356 9.18455 2.99721 7.13631 4.39828 5.49706C5.79935 3.85781 7.69279 2.71537 9.79619 2.24013C11.8996 1.7649 14.1003 1.98232 16.07 2.85999" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M22 4L12 14.01L9 11.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-text-secondary text-sm">Flashcards Due</div>
                <div class="text-2xl font-bold">24</div>
            </div>
            <div class="w-12 h-12 rounded-full bg-orange-100 flex items-center justify-center">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" class="text-orange-600">
                    <path d="M19 11H5M19 11C20.1046 11 21 11.8954 21 13V19C21 20.1046 20.1046 21 19 21H5C3.89543 21 3 20.1046 3 19V13C3 11.8954 3.89543 11 5 11M19 11V9C19 7.89543 18.1046 7 17 7M5 11V9C5 7.89543 5.89543 7 7 7M7 7V5C7 3.89543 7.89543 3 9 3H15C16.1046 3 17 3.89543 17 5V7M7 7H17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="card mb-6">
    <div class="card-header">
        <h2 class="card-title">Recent Activity</h2>
        <a href="#" class="text-sm text-primary font-medium">View All</a>
    </div>
    <div class="space-y-4">
        <?php
        $activities = [
            ['icon' => '✅', 'text' => 'Completed "Math homework" todo', 'time' => '10 minutes ago'],
            ['icon' => '📚', 'text' => 'Studied with Pomodoro for 25 minutes', 'time' => '1 hour ago'],
            ['icon' => '📅', 'text' => 'Added "Group meeting" to calendar', 'time' => '2 hours ago'],
            ['icon' => '💪', 'text' => 'Logged "Morning exercise" habit', 'time' => '5 hours ago'],
            ['icon' => '🧠', 'text' => 'Reviewed 15 flashcards', 'time' => 'Yesterday'],
        ];
        
        foreach ($activities as $activity):
        ?>
        <div class="flex items-center gap-3 p-3 rounded-lg hover:bg-bg-input transition-colors">
            <div class="w-8 h-8 rounded-full bg-bg-input flex items-center justify-center">
                <?php echo $activity['icon']; ?>
            </div>
            <div class="flex-1">
                <div class="font-medium"><?php echo $activity['text']; ?></div>
                <div class="text-sm text-text-secondary"><?php echo $activity['time']; ?></div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Quick Actions -->
<div>
    <h3 class="text-lg font-bold mb-4">Quick Actions</h3>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <a href="/todo" 
           class="btn btn-secondary flex flex-col items-center justify-center h-24">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" class="mb-2">
                <path d="M9 11L12 14L22 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M21 12V19C21 19.5304 20.7893 20.0391 20.4142 20.4142C20.0391 20.7893 19.5304 21 19 21H5C4.46957 21 3.96086 20.7893 3.58579 20.4142C3.21071 20.0391 3 19.5304 3 19V5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Add Todo
        </a>
        
        <a href="/calendar" 
           class="btn btn-secondary flex flex-col items-center justify-center h-24">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" class="mb-2">
                <path d="M8 7V3M16 7V3M7 11H17M5 21H19C20.1046 21 21 20.1046 21 19V7C21 5.89543 20.1046 5 19 5H5C3.89543 5 3 5.89543 3 7V19C3 20.1046 3.89543 21 5 21Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Add Event
        </a>
        
        <a href="/pomodoro" 
           class="btn btn-secondary flex flex-col items-center justify-center h-24">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" class="mb-2">
                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                <path d="M12 6V12L16 14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Start Timer
        </a>
        
        <a href="/flashcards" 
           class="btn btn-secondary flex flex-col items-center justify-center h-24">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" class="mb-2">
                <path d="M19 11H5M19 11C20.1046 11 21 11.8954 21 13V19C21 20.1046 20.1046 21 19 21H5C3.89543 21 3 20.1046 3 19V13C3 11.8954 3.89543 11 5 11M19 11V9C19 7.89543 18.1046 7 17 7M5 11V9C5 7.89543 5.89543 7 7 7M7 7V5C7 3.89543 7.89543 3 9 3H15C16.1046 3 17 3.89543 17 5V7M7 7H17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Study Cards
        </a>
    </div>
</div>
<?php

// $content = ob_get_clean();

// Include layout
// require_once __DIR__ . '/../layouts/layout.php';
