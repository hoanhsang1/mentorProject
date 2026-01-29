<?php
/**
 * Admin Sidebar Navigation Component
 * 
 * @param string $admin_tab Current admin tab
 */
?>
<aside class="sidebar admin-sidebar">
    <!-- Admin Logo/Header -->
    
    
    <!-- Admin Navigation Tabs -->
    <div class="nav-section">
        <h3 class="nav-title">Navigation</h3>
        <ul class="nav-list">
            <li class="nav-item">
                <a href="javascript:void(0)" 
                   class="nav-link admin-tab-link <?php echo ($admin_tab ?? 'dashboard') === 'dashboard' ? 'active' : ''; ?>"
                   data-tab="dashboard">
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
            <li class="nav-item">
                <a href="javascript:void(0)" 
                   class="nav-link admin-tab-link <?php echo ($admin_tab ?? '') === 'users' ? 'active' : ''; ?>"
                   data-tab="users">
                    <svg class="nav-icon" viewBox="0 0 20 20" fill="none">
                        <circle cx="7" cy="5" r="2" stroke="currentColor" stroke-width="1.5"/>
                        <path d="M10 14H4C2.89543 14 2 13.1046 2 12V10C2 8.89543 2.89543 8 4 8H10C11.1046 8 12 8.89543 12 10V12C12 13.1046 11.1046 14 10 14Z" 
                              stroke="currentColor" stroke-width="1.5"/>
                        <circle cx="15" cy="5" r="2" stroke="currentColor" stroke-width="1.5"/>
                        <path d="M18 14H12C10.8954 14 10 13.1046 10 12V10C10 8.89543 10.8954 8 12 8H18C19.1046 8 20 8.89543 20 10V12C20 13.1046 19.1046 14 18 14Z" 
                              stroke="currentColor" stroke-width="1.5"/>
                    </svg>
                    Users
                </a>
            </li>
            <li class="nav-item">
                <a href="javascript:void(0)" 
                   class="nav-link admin-tab-link <?php echo ($admin_tab ?? '') === 'pomodoro' ? 'active' : ''; ?>"
                   data-tab="pomodoro">
                    <svg class="nav-icon" viewBox="0 0 20 20" fill="none">
                        <circle cx="10" cy="10" r="8" stroke="currentColor" stroke-width="1.5"/>
                        <path d="M10 6V10L12 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Pomodoro Stats
                </a>
            </li>
            <li class="nav-item">
                <a href="javascript:void(0)" 
                   class="nav-link admin-tab-link <?php echo ($admin_tab ?? '') === 'activity' ? 'active' : ''; ?>"
                   data-tab="activity">
                    <svg class="nav-icon" viewBox="0 0 20 20" fill="none">
                        <path d="M2 10H18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        <path d="M10 2V18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        <circle cx="10" cy="10" r="3" stroke="currentColor" stroke-width="1.5"/>
                        <path d="M4 4L16 16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        <path d="M4 16L16 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                    Activity
                </a>
            </li>
        </ul>
    </div>
    
    <!-- Quick Actions -->
    <div class="nav-section mt-auto">
        <h3 class="nav-title">Quick Actions</h3>
        <ul class="nav-list">
            <li class="nav-item">
                <a href="/logout" class="nav-link text-red-400 hover:text-red-300">
                    <svg class="nav-icon" viewBox="0 0 20 20" fill="none">
                        <path d="M13 7L17 11M17 11L13 15M17 11H7M11 17H5C3.89543 17 3 16.1046 3 15V5C3 3.89543 3.89543 3 5 3H11" 
                              stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Logout
                </a>
            </li>
        </ul>
    </div>
</aside>

<!-- Mobile Overlay -->
<div class="overlay"></div>

<!-- JavaScript để xử lý admin tabs -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Xử lý click vào admin tabs
    const adminTabLinks = document.querySelectorAll('.admin-tab-link');
    
    adminTabLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const tabId = this.getAttribute('data-tab');
            
            // Remove active class từ tất cả links
            adminTabLinks.forEach(l => l.classList.remove('active'));
            
            // Add active class cho link được click
            this.classList.add('active');
            
            // Kích hoạt tab tương ứng
            switchTab(tabId);
            
            // Update URL hash
            window.history.pushState(null, null, `#${tabId}`);
        });
    });
    
    // Hàm chuyển tab
    function switchTab(tabId) {
        // Remove active class từ tất cả tab content
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.remove('active');
        });
        
        // Add active class cho tab content
        const tabContent = document.getElementById(`${tabId}-tab`);
        if (tabContent) {
            tabContent.classList.add('active');
            
            // Load content cho tab
            switch(tabId) {
                case 'dashboard':
                    loadDashboard();
                    break;
                case 'users':
                    loadUsers();
                    break;
                case 'pomodoro':
                    loadPomodoroStats();
                    break;
                case 'activity':
                    loadActivity();
                    break;
            }
        }
    }
    
    // Kiểm tra nếu URL có hash để load tab cụ thể
    const hash = window.location.hash.substring(1);
    if (hash && ['dashboard', 'users', 'pomodoro', 'activity'].includes(hash)) {
        // Tìm và kích hoạt tab từ sidebar
        const tabLink = document.querySelector(`.admin-tab-link[data-tab="${hash}"]`);
        if (tabLink) {
            setTimeout(() => tabLink.click(), 100);
        }
    } else {
        // Mặc định load dashboard
        setTimeout(() => {
            const defaultTab = document.querySelector('.admin-tab-link.active');
            if (defaultTab) {
                const tabId = defaultTab.getAttribute('data-tab');
                switchTab(tabId);
            }
        }, 100);
    }
});
</script>