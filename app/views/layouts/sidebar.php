<?php
/**
 * Sidebar Navigation Component
 */
?>
<aside class="sidebar">
    <!-- Main Navigation -->
    <div class="nav-section">
        <h3 class="nav-title">Main</h3>
        <ul class="nav-list">
            <li class="nav-item">
                <a href="/dashboard" 
                   class="nav-link <?php echo $current_page === 'dashboard' ? 'active' : ''; ?>">
                    <svg class="nav-icon" viewBox="0 0 20 20" fill="none">
                        <path d="M3 4C3 3.44772 3.44772 3 4 3H8C8.55228 3 9 3.44772 9 4V8C9 8.55228 8.55228 9 8 9H4C3.44772 9 3 8.55228 3 8V4Z" 
                              stroke="currentColor" stroke-width="1.5"/>
                        <path d="M3 12C3 11.4477 3.44772 11 4 11H8C8.55228 11 9 11.4477 9 12V16C9 16.5523 8.55228 17 8 17H4C3.44772 17 3 16.5523 3 16V12Z" 
                              stroke="currentColor" stroke-width="1.5"/>
                        <path d="M11 4C11 3.44772 11.4477 3 12 3H16C16.5523 3 17 3.44772 17 4V8C17 8.55228 16.5523 9 16 9H12C11.4477 9 11 8.55228 11 8V4Z" 
                              stroke="currentColor" stroke-width="1.5"/>
                        <path d="M11 12C11 11.4477 11.4477 11 12 11H16C16.5523 11 17 11.4477 17 12V16C17 16.5523 16.5523 17 16 17H12C11.4477 17 11 16.5523 11 16V12Z" 
                              stroke="currentColor" stroke-width="1.5"/>
                    </svg>
                    Dashboard
                </a>
            </li>
        </ul>
    </div>
    
    <!-- Study Tools -->
    <div class="nav-section">
        <h3 class="nav-title">Study Tools</h3>
        <ul class="nav-list">
            <li class="nav-item">
                <a href="/todo" 
                   class="nav-link <?php echo $current_page === 'todo' ? 'active' : ''; ?>">
                    <svg class="nav-icon" viewBox="0 0 20 20" fill="none">
                        <path d="M5 3H15C16.1046 3 17 3.89543 17 5V15C17 16.1046 16.1046 17 15 17H5C3.89543 17 3 16.1046 3 15V5C3 3.89543 3.89543 3 5 3Z" 
                              stroke="currentColor" stroke-width="1.5"/>
                        <path d="M7 10L9 12L13 8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Todo List
                </a>
            </li>
            <li class="nav-item">
                <a href="/calendar" 
                   class="nav-link <?php echo $current_page === 'calendar' ? 'active' : ''; ?>">
                    <svg class="nav-icon" viewBox="0 0 20 20" fill="none">
                        <path d="M6 2V4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M14 2V4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M3 8H17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M4 4H16C16.5523 4 17 4.44772 17 5V15C17 15.5523 16.5523 16 16 16H4C3.44772 16 3 15.5523 3 15V5C3 4.44772 3.44772 4 4 4Z" 
                              stroke="currentColor" stroke-width="1.5"/>
                    </svg>
                    Calendar
                </a>
            </li>
            <li class="nav-item">
                <a href="/pomodoro" 
                   class="nav-link <?php echo $current_page === 'pomodoro' ? 'active' : ''; ?>">
                    <svg class="nav-icon" viewBox="0 0 20 20" fill="none">
                        <circle cx="10" cy="10" r="8" stroke="currentColor" stroke-width="1.5"/>
                        <path d="M10 6V10L12 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Pomodoro Timer
                </a>
            </li>
            <li class="nav-item">
                <a href="/habit" 
                   class="nav-link <?php echo $current_page === 'habit' ? 'active' : ''; ?>">
                    <svg class="nav-icon" viewBox="0 0 20 20" fill="none">
                        <path d="M10 17C13.866 17 17 13.866 17 10C17 6.13401 13.866 3 10 3C6.13401 3 3 6.13401 3 10C3 13.866 6.13401 17 10 17Z" 
                              stroke="currentColor" stroke-width="1.5"/>
                        <path d="M7 10L9 12L13 8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Habit Tracker
                </a>
            </li>
            <li class="nav-item">
                <a href="/flashcards" 
                   class="nav-link <?php echo $current_page === 'flashcards' ? 'active' : ''; ?>">
                    <svg class="nav-icon" viewBox="0 0 20 20" fill="none">
                        <path d="M3 7H17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        <path d="M3 13H17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        <path d="M5 5V9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        <path d="M5 11V15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        <rect x="3" y="3" width="14" height="14" rx="2" stroke="currentColor" stroke-width="1.5"/>
                    </svg>
                    Flashcards
                </a>
            </li>
        </ul>
    </div>
    
    <!-- Account -->
    <div class="nav-section">
        <h3 class="nav-title">Account</h3>
        <ul class="nav-list">
            <li class="nav-item">
                <a href="/profile" 
                   class="nav-link <?php echo $current_page === 'profile' ? 'active' : ''; ?>">
                    <svg class="nav-icon" viewBox="0 0 20 20" fill="none">
                        <circle cx="10" cy="7" r="4" stroke="currentColor" stroke-width="1.5"/>
                        <path d="M5 17C5 13.6863 7.68629 11 11 11H13C16.3137 11 19 13.6863 19 17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                    Profile
                </a>
            </li>
            <li class="nav-item">
                <a href="/settings" 
                   class="nav-link <?php echo $current_page === 'settings' ? 'active' : ''; ?>">
                    <svg class="nav-icon" viewBox="0 0 20 20" fill="none">
                        <circle cx="10" cy="10" r="2" stroke="currentColor" stroke-width="1.5"/>
                        <path d="M16 10C16 10.69 15.93 11.36 15.8 12H18.5C18.78 12 19 12.22 19 12.5V15.5C19 15.78 18.78 16 18.5 16H15.8C15.93 16.64 16 17.31 16 18C16 18.69 15.93 19.36 15.8 20H18.5C18.78 20 19 20.22 19 20.5V23.5C19 23.78 18.78 24 18.5 24H15.8C15.29 24.74 14.65 25.39 13.91 25.9L13.9 28.5C13.9 28.78 13.68 29 13.4 29H10.4C10.12 29 9.9 28.78 9.9 28.5L9.91 25.9C9.17 25.39 8.53 24.74 8.02 24H5.5C5.22 24 5 23.78 5 23.5V20.5C5 20.22 5.22 20 5.5 20H8.02C7.89 19.36 7.82 18.69 7.82 18C7.82 17.31 7.89 16.64 8.02 16H5.5C5.22 16 5 15.78 5 15.5V12.5C5 12.22 5.22 12 5.5 12H8.02C8.53 11.26 9.17 10.61 9.91 10.1L9.9 7.5C9.9 7.22 10.12 7 10.4 7H13.4C13.68 7 13.9 7.22 13.9 7.5L13.91 10.1C14.65 10.61 15.29 11.26 15.8 12Z" 
                              stroke="currentColor" stroke-width="1.5"/>
                    </svg>
                    Settings
                </a>
            </li>
        </ul>
    </div>
</aside>

<!-- Mobile Overlay -->
<div class="overlay"></div>