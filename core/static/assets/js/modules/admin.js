// Global variables
let currentUserId = '';
let currentUserPage = 1;
let chartInstances = {};
let currentActivityPage = 1;


// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function () {
    // Check if Chart.js is loaded
    if (typeof Chart === 'undefined') {
        console.warn('⚠️ Chart.js not detected in initial load.');
    }

    // Auto load tab dựa trên URL hash hoặc mặc định
    const hash = window.location.hash.substring(1);
    const validTabs = ['dashboard', 'users', 'pomodoro', 'activity'];

    // Setup modal click handlers
    setupModalOutsideClick();

    // Setup all close buttons
    document.querySelectorAll('.modal-close, [data-close-modal]').forEach(button => {
        button.addEventListener('click', function () {
            const modalId = this.getAttribute('data-close-modal') ||
                this.closest('.admin-modal')?.id;
            if (modalId) {
                closeModal(modalId);
            }
        });
    });

    // Setup tab switching
    document.querySelectorAll('[data-tab], [data-admin-tab]').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const tabId = this.getAttribute('data-tab') || this.getAttribute('data-admin-tab');
            if (tabId) {
                switchTab(tabId);
            }
        });
    });

    // Start with default tab
    setTimeout(() => {
        const initialTab = (hash && validTabs.includes(hash)) ? hash : 'dashboard';
        switchTab(initialTab);
    }, 300);
});

function switchTab(tabId) {
    console.log(`🔄 Switching to tab: ${tabId}`);

    // Update tab buttons
    document.querySelectorAll('.admin-tab-btn, [data-admin-tab]').forEach(btn => {
        const btnTabId = btn.getAttribute('data-tab') || btn.getAttribute('data-admin-tab');
        if (btnTabId === tabId) {
            btn.classList.add('active');
        } else {
            btn.classList.remove('active');
        }
    });

    // Update tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        if (content.id === `${tabId}-tab`) {
            content.classList.add('active');
        } else {
            content.classList.remove('active');
        }
    });

    // Load data for the tab
    switch (tabId) {
        case 'dashboard':
            loadDashboard();
            break;
        case 'users':
            loadUsers(1);
            break;
        case 'pomodoro':
            loadPomodoroStats();
            break;
        case 'activity':
            loadActivity(1);
            break;
    }

    // Update URL hash without jumping
    window.history.pushState(null, null, `#${tabId}`);
}

// Setup click outside to close modal
function setupModalCloseHandlers() {
    // Handle click outside modal
    document.addEventListener('click', function (event) {
        if (activeModal && !event.target.closest('.modal-content') && !event.target.closest('[data-modal]')) {
            closeModal(activeModal);
        }
    });

    // Handle ESC key
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && activeModal) {
            closeModal(activeModal);
        }
    });
}

// Dashboard functions
function loadDashboard() {
    showLoading('dashboard');

    console.log("🚀 Fetching dashboard data from: /management/api?action=get_stats");

    fetch('/management/api?action=get_stats')
        .then(response => {
            console.log("📥 Response status:", response.status, response.statusText);
            console.log("📥 Response headers:", Object.fromEntries(response.headers.entries()));
            return response.text(); // Dùng text() thay vì json() để xem raw
        })
        .then(text => {
            console.log("📄 Raw response (first 500 chars):", text.substring(0, 500));
            console.log("📄 Raw response length:", text.length);

            // Kiểm tra ký tự đầu tiên
            console.log("🔍 First 10 characters:",
                text.substring(0, 10).split('').map(c => c.charCodeAt(0)));

            try {
                const data = JSON.parse(text);
                console.log("✅ JSON parsed successfully:", data);
                if (data.success) {
                    renderDashboard(data);
                    hideLoading('dashboard');
                } else {
                    console.error("❌ API error:", data.error);
                    App.showToast(data.error || 'Failed to load dashboard', 'error');
                    hideLoading('dashboard');
                }
            } catch (e) {
                console.error("❌ JSON parse error:", e);
                console.error("🔍 Raw text that failed (first 100 chars):",
                    text.substring(0, 100).replace(/\n/g, '\\n').replace(/\r/g, '\\r'));
                App.showToast('Invalid JSON response from server', 'error');
                hideLoading('dashboard');
            }
        })
        .catch(error => {
            console.error('❌ Network error:', error);
            App.showToast('Error loading dashboard: ' + error.message, 'error');
            hideLoading('dashboard');
        });
}

function renderDashboard(data) {
    // Render stats cards
    const statsGrid = document.getElementById('stats-grid');
    statsGrid.innerHTML = `
        <div class="stat-card primary">
            <div class="stat-value">${data.total_users}</div>
            <div class="stat-label">Total Users</div>
            <div class="stat-trend positive">+${data.new_users_30_days} new (30 days)</div>
        </div>
        <div class="stat-card success">
            <div class="stat-value">${data.total_study_hours}h</div>
            <div class="stat-label">Total Study Time</div>
            <div class="stat-trend">${data.total_sessions} sessions</div>
        </div>
        <div class="stat-card warning">
            <div class="stat-value">${data.active_users}</div>
            <div class="stat-label">Active Users</div>
            <div class="stat-trend">Last 7 days</div>
        </div>
        <div class="stat-card info">
            <div class="stat-value">${Math.round(data.total_study_hours / data.total_users * 10) / 10 || 0}</div>
            <div class="stat-label">Avg Hours/User</div>
            <div class="stat-trend">Overall average</div>
        </div>
    `;

    // Create charts if Chart.js is loaded
    if (typeof Chart !== 'undefined') {
        createStudyTimeChart(data.daily_stats);
        createActivityChart(data.daily_stats);
    } else {
        // Wait for Chart.js to load
        console.log("⏳ Chart.js not ready, retrying in 500ms...");
        setTimeout(() => renderDashboard(data), 500);
    }
}

function createStudyTimeChart(dailyStats) {
    const ctx = document.getElementById('studyTimeChart').getContext('2d');

    // Destroy existing chart instance
    if (chartInstances['studyTime']) {
        chartInstances['studyTime'].destroy();
    }

    // Prepare data
    const labels = dailyStats.map(stat => stat.date);
    const studyHours = dailyStats.map(stat => Math.round(stat.minutes / 60 * 10) / 10);

    chartInstances['studyTime'] = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Study Hours',
                data: studyHours,
                borderColor: '#4a6cf7',
                backgroundColor: 'rgba(74, 108, 247, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Hours'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Date'
                    }
                }
            }
        }
    });
}

function createActivityChart(dailyStats) {
    const ctx = document.getElementById('activityChart').getContext('2d');

    if (chartInstances['activity']) {
        chartInstances['activity'].destroy();
    }

    const labels = dailyStats.map(stat => stat.date);
    const sessions = dailyStats.map(stat => stat.sessions);

    chartInstances['activity'] = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Sessions',
                data: sessions,
                backgroundColor: '#10b981',
                borderColor: '#059669',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Sessions'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Date'
                    }
                }
            }
        }
    });
}

// Users management
function loadUsers(page = 1) {
    showLoading('users');
    currentUserPage = page;

    const search = document.getElementById('user-search').value;
    const params = new URLSearchParams({
        action: 'get_users',
        page: page,
        limit: 10,
        search: search
    });

    fetch(`/management/api?${params}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderUsers(data);
                hideLoading('users');
            } else {
                App.showToast(data.error || 'Failed to load users', 'error');
                hideLoading('users');
            }
        })
        .catch(error => {
            console.error('Error loading users:', error);
            App.showToast('Error loading users', 'error');
            hideLoading('users');
        });
}

function renderUsers(data) {
    const tbody = document.getElementById('users-table-body');
    const pagination = document.getElementById('users-pagination');

    // Clear existing rows
    tbody.innerHTML = '';

    // Render users
    data.users.forEach((user, index) => {
        const row = document.createElement('tr');
        row.id = `user-row-${user.user_id}`;
        row.className = 'border-b border-border hover:bg-bg-input';

        const statusBadge = user.is_deleted
            ? '<span class="badge badge-error">Inactive</span>'
            : '<span class="badge badge-success">Active</span>';

        const roleBadge = user.role === 'admin'
            ? '<span class="badge badge-primary mr-1">Admin</span>'
            : '<span class="badge badge-secondary mr-1">User</span>';

        const joinedDate = new Date(user.created_at).toLocaleDateString('vi-VN', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });

        row.innerHTML = `
            <td class="p-4">
                <div class="font-mono text-xs text-text-secondary">${user.user_id.substring(0, 8)}...</div>
            </td>
            <td class="p-4">
                <div class="font-medium text-text">${user.username}</div>
                ${roleBadge}
            </td>
            <td class="p-4 text-text">${user.fullname || '-'}</td>
            <td class="p-4 text-text">${user.email || '-'}</td>
            <td class="p-4">${statusBadge}</td>
            <td class="p-4 text-text-secondary text-sm">${joinedDate}</td>
            <td class="p-4">
                <div class="flex space-x-2">
                    <button class="p-2 text-primary hover:bg-primary hover:bg-opacity-10 rounded" 
                            onclick="viewUserDetail('${user.user_id}')" 
                            title="View Details">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
                    <button class="p-2 text-warning hover:bg-warning hover:bg-opacity-10 rounded" 
                            onclick="showResetPasswordModal('${user.user_id}')" 
                            title="Reset Password">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                        </svg>
                    </button>
                    <button class="p-2 ${user.is_deleted ? 'text-success hover:bg-success hover:bg-opacity-10' : 'text-warning hover:bg-warning hover:bg-opacity-10'} rounded" 
                            onclick="toggleUserStatus('${user.user_id}', ${user.is_deleted}, ${index})" 
                            title="${user.is_deleted ? 'Activate User' : 'Deactivate User'}">
                        ${user.is_deleted ? `
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        ` : `
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        `}
                    </button>
                    <button class="p-2 text-error hover:bg-error hover:bg-opacity-10 rounded" 
                            onclick="deleteUser('${user.user_id}', ${index})" 
                            title="Delete User">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                        </svg>
                    </button>
                </div>
            </td>
        `;

        tbody.appendChild(row);
    });

    // Render pagination
    renderPagination(pagination, data.pagination);
}
// Update user row - dùng utility classes
function updateUserRow(userId, userData) {
    const row = document.getElementById(`user-row-${userId}`);
    if (!row) return;

    const statusBadge = userData.is_deleted
        ? '<span class="badge badge-error">Inactive</span>'
        : '<span class="badge badge-success">Active</span>';

    const roleBadge = userData.role === 'admin'
        ? '<span class="badge badge-primary mr-1">Admin</span>'
        : '<span class="badge badge-secondary mr-1">User</span>';

    const joinedDate = new Date(userData.created_at).toLocaleDateString('vi-VN', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });

    row.innerHTML = `
        <td class="p-4">
            <div class="font-mono text-xs text-text-secondary">${userData.user_id.substring(0, 8)}...</div>
        </td>
        <td class="p-4">
            <div class="font-medium text-text">${userData.username}</div>
            ${roleBadge}
        </td>
        <td class="p-4 text-text">${userData.fullname || '-'}</td>
        <td class="p-4 text-text">${userData.email || '-'}</td>
        <td class="p-4">${statusBadge}</td>
        <td class="p-4 text-text-secondary text-sm">${joinedDate}</td>
        <td class="p-4">
            <div class="flex space-x-2">
                <button class="p-2 text-primary hover:bg-primary hover:bg-opacity-10 rounded" 
                        onclick="viewUserDetail('${userData.user_id}')" 
                        title="View Details">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                </button>
                <button class="p-2 text-warning hover:bg-warning hover:bg-opacity-10 rounded" 
                        onclick="showResetPasswordModal('${userData.user_id}')" 
                        title="Reset Password">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                </button>
                <button class="p-2 ${userData.is_deleted ? 'text-success hover:bg-success hover:bg-opacity-10' : 'text-warning hover:bg-warning hover:bg-opacity-10'} rounded" 
                        onclick="toggleUserStatus('${userData.user_id}', ${userData.is_deleted})" 
                        title="${userData.is_deleted ? 'Activate User' : 'Deactivate User'}">
                    ${userData.is_deleted ? `
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    ` : `
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    `}
                </button>
                <button class="p-2 text-error hover:bg-error hover:bg-opacity-10 rounded" 
                        onclick="deleteUser('${userData.user_id}')" 
                        title="Delete User">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="3 6 5 6 21 6"></polyline>
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                    </svg>
                </button>
            </div>
        </td>
    `;
}

function renderPagination(element, pagination) {
    element.innerHTML = '';

    const totalPages = pagination.total_pages;
    const currentPage = pagination.page;

    // Previous button
    const prevButton = document.createElement('button');
    prevButton.className = 'page-btn';
    prevButton.textContent = '←';
    prevButton.disabled = currentPage === 1;
    prevButton.onclick = () => loadUsers(currentPage - 1);
    element.appendChild(prevButton);

    // Page numbers
    const startPage = Math.max(1, currentPage - 2);
    const endPage = Math.min(totalPages, startPage + 4);

    for (let i = startPage; i <= endPage; i++) {
        const pageButton = document.createElement('button');
        pageButton.className = `page-btn ${i === currentPage ? 'active' : ''}`;
        pageButton.textContent = i;
        pageButton.onclick = () => loadUsers(i);
        element.appendChild(pageButton);
    }

    // Next button
    const nextButton = document.createElement('button');
    nextButton.className = 'page-btn';
    nextButton.textContent = '→';
    nextButton.disabled = currentPage === totalPages;
    nextButton.onclick = () => loadUsers(currentPage + 1);
    element.appendChild(nextButton);

    // Page info
    const info = document.createElement('span');
    info.className = 'page-info';
    info.textContent = `Page ${currentPage} of ${totalPages} (${pagination.total} total users)`;
    element.appendChild(info);
}

function viewUserDetail(userId) {
    document.getElementById('user-detail-content').innerHTML = `
        <div class="text-center py-8">
            <div class="loading-spinner"></div>
            <p class="mt-2 text-text-secondary">Loading user details...</p>
        </div>
    `;
    openModalSafely('user-detail-modal', event);

    fetch(`/management/api?action=get_user_detail&user_id=${userId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderUserDetail(data);
            } else {
                document.getElementById('user-detail-content').innerHTML =
                    `<p class="text-red-600">Error: ${data.error}</p>`;
            }
        })
        .catch(error => {
            console.error('Error loading user detail:', error);
            document.getElementById('user-detail-content').innerHTML =
                '<p class="text-red-600">Error loading user details</p>';
        });
}

function renderUserDetail(data) {
    const user = data.user;
    const stats = data.pomodoro_stats;
    const activity = data.recent_activity;

    // Format functions
    const getAvatarText = (name) => {
        if (!name) return '?';
        const names = name.split(' ');
        if (names.length >= 2) {
            return (names[0][0] + names[names.length - 1][0]).toUpperCase();
        }
        return name.substring(0, 2).toUpperCase();
    };

    const formatDate = (dateString) => {
        if (!dateString) return '-';
        return new Date(dateString).toLocaleDateString('vi-VN', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    };

    // Tính progress
    const progressPercent = stats && stats.total_minutes > 0
        ? Math.min(Math.round((stats.total_minutes / (100 * 60)) * 100), 100)
        : 0;

    // Tạo activity HTML
    let activityHtml = '';
    if (activity && activity.length > 0) {
        activityHtml = `
            <div class="mb-6">
                <h4 class="text-lg font-semibold mb-4 text-text">Recent Activity</h4>
                <div class="space-y-3">
                    ${activity.slice(0, 5).map(session => `
                        <div class="flex items-start p-3 bg-bg-input rounded-lg">
                            <div class="activity-icon">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="font-medium text-text">${session.study_topic || 'No topic'}</div>
                                <div class="flex justify-between text-sm text-text-secondary mt-1">
                                    <span>${session.duration_minutes || 0} minutes</span>
                                    <span>${formatDate(session.start_time)}</span>
                                </div>
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>
        `;
    } else {
        activityHtml = `
            <div class="mb-6">
                <h4 class="text-lg font-semibold mb-4 text-text">Recent Activity</h4>
                <div class="recent-activity text-center py-6 bg-bg-input rounded-lg">
                    <i class="fa-regular fa-clipboard"></i>
                    <p class="text-text-secondary">No activity recorded yet</p>
                </div>
            </div>
        `;
    }

    // Status và role badges
    const statusBadge = user.is_deleted
        ? '<span class="badge badge-error">Inactive</span>'
        : '<span class="badge badge-success">Active</span>';

    const roleBadge = user.role === 'admin'
        ? '<span class="badge badge-primary">Admin</span>'
        : '<span class="badge badge-secondary">User</span>';

    // User stats nếu có
    let statsHtml = '';
    if (stats && (stats.total_sessions > 0 || stats.total_minutes > 0)) {
        const studyHours = Math.round(stats.total_minutes / 60 * 10) / 10;
        statsHtml = `
            <div class="mb-6">
                <h4 class="text-lg font-semibold mb-4 text-text">Study Statistics</h4>
                <div class="flex grid grid-cols-3 gap-4 mb-4">
                    <div class="card w-33 text-center p-4 bg-bg-input rounded-lg">
                        <div class="text-2xl font-bold text-primary">${stats.total_sessions || 0}</div>
                        <div class="text-sm text-text-secondary mt-1">Sessions</div>
                    </div>
                    <div class="card w-33 text-center p-4 bg-bg-input rounded-lg">
                        <div class="text-2xl font-bold text-primary">${studyHours}</div>
                        <div class="text-sm text-text-secondary mt-1">Study Hours</div>
                    </div>
                    <div class="card w-33 text-center p-4 bg-bg-input rounded-lg">
                        <div class="text-2xl font-bold text-primary">${Math.round(stats.avg_minutes) || 0}</div>
                        <div class="text-sm text-text-secondary mt-1">Avg/Session</div>
                    </div>
                </div>
                
                <div class="p-4 bg-bg-input rounded-lg">
                    <div class="flex justify-between text-sm text-text-secondary mb-2">
                        <span>Study Progress</span>
                        <span>${progressPercent}%</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar" style="width: ${progressPercent}%"></div>
                    </div>
                    <div class="text-center text-xs text-text-secondary mt-2">
                        ${studyHours} / 100 hours
                    </div>
                </div>
            </div>
        `;
    }

    document.getElementById('user-detail-content').innerHTML = `
        <div class="space-y-6">
            <!-- User Profile -->
            <div class="text-center">
            
                <h3 class="text-xl font-bold text-text mb-1">${user.fullname || 'No name'}</h3>
                <p class="text-text-secondary mb-3">@${user.username}</p>
                <div class="flex justify-center space-x-2">
                    ${roleBadge}
                    ${statusBadge}
                </div>
            </div>
            
            <!-- Basic Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Username</label>
                    <div class="p-2 bg-bg-input rounded">${user.username}</div>
                </div>
                <div>
                    <label class="form-label">Email</label>
                    <div class="p-2 bg-bg-input rounded">${user.email || '-'}</div>
                </div>
                <div>
                    <label class="form-label">Full Name</label>
                    <div class="p-2 bg-bg-input rounded">${user.fullname || '-'}</div>
                </div>
                <div>
                    <label class="form-label">Account Created</label>
                    <div class="p-2 bg-bg-input rounded">${formatDate(user.created_at)}</div>
                </div>
                <div>
                    <label class="form-label">Last Updated</label>
                    <div class="p-2 bg-bg-input rounded">${formatDate(user.updated_at)}</div>
                </div>
                <div>
                    <label class="form-label">Role</label>
                    <div class="p-2">
                        ${roleBadge}
                    </div>
                </div>
            </div>
            
            ${statsHtml}
            ${activityHtml}
            
            <!-- Actions -->
            <div class="flex justify-center space-x-3 pt-6 border-t border-border">
                <button class="btn btn-primary" onclick="showResetPasswordModal('${user.user_id}')">
                    Reset Password
                </button>
                <button class="btn ${user.is_deleted ? 'btn-success' : 'btn-warning'}" 
                        onclick="toggleUserStatus('${user.user_id}', ${user.is_deleted}); closeModal('user-detail-modal')">
                    ${user.is_deleted ? 'Activate User' : 'Deactivate User'}
                </button>
            </div>
        </div>
    `;
}
function showResetPasswordModal(userId, event) {
    if (event) event.stopPropagation();

    currentUserId = userId;
    document.getElementById('reset-password-result').innerHTML = '';
    openModalSafely('reset-password-modal', event);
}


function confirmResetPassword() {
    const formData = new FormData();
    formData.append('action', 'reset_password');
    formData.append('user_id', currentUserId);

    // Show loading trong modal
    const resultDiv = document.getElementById('reset-password-result');
    resultDiv.innerHTML = `
        <div class="text-center py-4">
            <div class="loading-spinner inline-block"></div>
            <p class="mt-2 text-sm">Resetting password...</p>
        </div>
    `;

    fetch('/management/api', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                resultDiv.innerHTML = `
                <div class="alert alert-success">
                    <div class="flex items-start">
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-green-800">Password reset successful!</h3>
                            <div class="mt-2 text-sm text-green-700">
                                <p>New password: <code class="font-mono bg-green-100 px-2 py-1 rounded">${data.new_password}</code></p>
                                <p class="mt-1 text-xs text-yellow-700">
                                    <i class="fa-solid fa-triangle-exclamation"></i>
                                    Make sure to save this password, it won't be shown again.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            `;

                // Auto close modal sau 5 giây
                setTimeout(() => {
                    closeModal('reset-password-modal');
                }, 5000);
            } else {
                resultDiv.innerHTML = `
                <div class="alert alert-error">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Error</h3>
                            <div class="mt-2 text-sm text-red-700">
                                <p>${data.error}</p>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            }
        })
        .catch(error => {
            console.error('Error resetting password:', error);
            resultDiv.innerHTML = `
            <div class="alert alert-error">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Network Error</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <p>Unable to reset password. Please try again.</p>
                        </div>
                    </div>
                </div>
            </div>
        `;
        });
}

function toggleUserStatus(userId, currentStatus, rowIndex) {
    if (!confirm(`Are you sure you want to ${currentStatus ? 'unlock' : 'lock'} this user?`)) {
        return;
    }

    const formData = new FormData();
    formData.append('action', 'toggle_user_status');
    formData.append('user_id', userId);

    // Hiển thị loading trên dòng
    const row = document.getElementById(`user-row-${userId}`);
    if (row) {
        const originalContent = row.innerHTML;
        row.innerHTML = `
            <td colspan="7" class="text-center py-4">
                <div class="inline-flex items-center">
                    <div class="loading-spinner" style="width: 16px; height: 16px;"></div>
                    <span class="ml-2 text-sm">Updating...</span>
                </div>
            </td>
        `;
    }

    fetch('/management/api', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const statusText = data.new_status ? 'locked' : 'unlocked';
                App.showToast(`User ${statusText} successfully`, 'success');

                // Lấy user data mới và update dòng
                fetch(`/management/api?action=get_user_detail&user_id=${userId}`)
                    .then(response => response.json())
                    .then(userData => {
                        if (userData.success) {
                            updateUserRow(userId, userData.user);
                        }
                    });
            } else {
                App.showToast(data.error || 'Failed to update user status', 'error');
                // Restore row nếu lỗi
                if (row) row.innerHTML = originalContent;
            }
        })
        .catch(error => {
            console.error('Error toggling user status:', error);
            App.showToast('Error updating user status', 'error');
            // Restore row nếu lỗi
            if (row) row.innerHTML = originalContent;
        });
}

function deleteUser(userId, rowIndex) {
    if (!confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        return;
    }

    const formData = new FormData();
    formData.append('action', 'delete_user');
    formData.append('user_id', userId);

    // Hiển thị loading trên dòng
    const row = document.getElementById(`user-row-${userId}`);
    if (row) {
        const originalContent = row.innerHTML;
        row.innerHTML = `
            <td colspan="7" class="text-center py-4">
                <div class="inline-flex items-center">
                    <div class="loading-spinner" style="width: 16px; height: 16px;"></div>
                    <span class="ml-2 text-sm">Deleting...</span>
                </div>
            </td>
        `;
    }

    fetch('/management/api', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                App.showToast('User deleted successfully', 'success');

                // Animate row removal
                if (row) {
                    row.style.transition = 'all 0.3s ease';
                    row.style.opacity = '0';
                    row.style.height = '0';
                    row.style.padding = '0';
                    row.style.margin = '0';

                    setTimeout(() => {
                        row.remove();

                        // Kiểm tra nếu table rỗng thì reload
                        const tbody = document.getElementById('users-table-body');
                        if (tbody.children.length === 0) {
                            loadUsers(currentUserPage);
                        }
                    }, 300);
                }
            } else {
                App.showToast(data.error || 'Failed to delete user', 'error');
                // Restore row nếu lỗi
                if (row) row.innerHTML = originalContent;
            }
        })
        .catch(error => {
            console.error('Error deleting user:', error);
            App.showToast('Error deleting user', 'error');
            // Restore row nếu lỗi
            if (row) row.innerHTML = originalContent;
        });
}

// Pomodoro stats
function loadPomodoroStats() {
    showLoading('pomodoro');

    const days = document.getElementById('pomodoro-days').value;

    fetch(`/management/api?action=get_pomodoro_stats&days=${days}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderPomodoroStats(data);
                hideLoading('pomodoro');
            } else {
                App.showToast(data.error || 'Failed to load pomodoro stats', 'error');
                hideLoading('pomodoro');
            }
        })
        .catch(error => {
            console.error('Error loading pomodoro stats:', error);
            App.showToast('Error loading pomodoro stats', 'error');
            hideLoading('pomodoro');
        });
}

function renderPomodoroStats(data) {
    const statsGrid = document.getElementById('pomodoro-stats');
    const stats = data.stats;

    statsGrid.innerHTML = `
        <div class="stat-card primary">
            <div class="stat-value">${stats.total_sessions || 0}</div>
            <div class="stat-label">Total Sessions</div>
        </div>
        <div class="stat-card success">
            <div class="stat-value">${Math.round(stats.total_minutes / 60 * 10) / 10 || 0}</div>
            <div class="stat-label">Total Hours</div>
        </div>
        <div class="stat-card warning">
            <div class="stat-value">${stats.active_users || 0}</div>
            <div class="stat-label">Active Users</div>
        </div>
        <div class="stat-card info">
            <div class="stat-value">${Math.round(stats.avg_session_length) || 0}</div>
            <div class="stat-label">Avg Session (min)</div>
        </div>
    `;

    // Create top users chart
    if (typeof Chart !== 'undefined' && data.top_users) {
        createTopUsersChart(data.top_users);
    }
}

function createTopUsersChart(topUsers) {
    const ctx = document.getElementById('topUsersChart').getContext('2d');

    if (chartInstances['topUsers']) {
        chartInstances['topUsers'].destroy();
    }

    const labels = topUsers.map(user => {
        const name = user.username || 'Guest';
        return name.substring(0, 10) + (name.length > 10 ? '...' : '');
    });
    const studyHours = topUsers.map(user => Math.round(user.total_minutes / 60 * 10) / 10);

    chartInstances['topUsers'] = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Study Hours',
                data: studyHours,
                backgroundColor: [
                    '#4a6cf7',
                    '#3a5bd9',
                    '#2a4bb8',
                    '#1a3b97',
                    '#0a2b76'
                ].slice(0, topUsers.length),
                borderColor: '#1a3b97',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Study Hours'
                    }
                },
                x: {
                    ticks: {
                        maxRotation: 45
                    }
                }
            }
        }
    });
}

function loadActivityTable(page, days) {
    const tbody = document.getElementById('activity-table-body');

    // Thêm animation fade out
    tbody.style.opacity = '0.5';
    tbody.style.transition = 'opacity 0.2s ease';

    const params = new URLSearchParams({
        action: 'get_activity_table',
        days: days,
        page: page,
        limit: 10
    });

    fetch(`/management/api?${params}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Hiệu ứng fade in
                setTimeout(() => {
                    renderActivityTable(data);
                    tbody.style.opacity = '1';
                    hideLoading('activity');
                }, 200);
            } else {
                App.showToast(data.error || 'Failed to load activity', 'error');
                hideLoading('activity');
                tbody.style.opacity = '1';
            }
        })
        .catch(error => {
            console.error('Error loading activity table:', error);
            App.showToast('Error loading activity', 'error');
            hideLoading('activity');
            tbody.style.opacity = '1';
        });
}

// Activity functions
function loadActivity(page = 1) {
    showLoading('activity');
    currentActivityPage = page;

    const days = document.getElementById('activity-days').value;

    // CHỈ LOAD CHART NẾU LÀ LẦN ĐẦU (page = 1)
    if (page === 1) {
        loadActivityChart(days);
    }

    // LUÔN LOAD TABLE VỚI PAGINATION
    loadActivityTable(page, days);
}

function loadActivityChart(days) {
    // Đảm bảo Chart.js đã load
    if (typeof Chart === 'undefined') {
        setTimeout(() => loadActivityChart(days), 100);
        return;
    }

    fetch(`/management/api?action=get_daily_activity&days=${days}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                createDailyActivityChart(data.daily_activity);
            }
        })
        .catch(error => {
            console.error('Error loading activity chart:', error);
        });
}

function renderActivityTable(data) {
    const activity = data.activity;

    // Render recent activity table
    const tbody = document.getElementById('activity-table-body');
    tbody.innerHTML = '';

    if (activity && activity.length > 0) {
        activity.forEach(session => {
            const row = document.createElement('tr');

            const duration = session.duration_minutes
                ? `${session.duration_minutes} min`
                : 'In progress';

            const topic = session.study_topic || session.pomodoro_title || 'No topic';
            const username = session.username || 'Unknown';

            row.innerHTML = `
                <td>
                    <div class="flex items-center">
                        <div class="user-avatar-small">
                            ${getUserAvatarText(username)}
                        </div>
                        <span class="ml-2">${username}</span>
                    </div>
                </td>
                <td>
                    <span class="status-badge ${session.status === 'completed' ? 'completed' : 'in-progress'}">
                        ${session.status}
                    </span>
                </td>
                <td>${duration}</td>
                <td>
                    <div class="max-w-xs truncate" title="${topic}">
                        ${topic}
                    </div>
                </td>
                <td>
                    <div class="text-xs text-text-secondary">
                        ${formatDateTime(session.start_time)}
                    </div>
                </td>
            `;

            tbody.appendChild(row);
        });
    } else {
        tbody.innerHTML = `
            <tr>
                <td colspan="5" class="text-center py-8 text-text-secondary">
                    <div class="flex flex-col items-center">
                        <svg class="w-12 h-12 mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <p class="text-sm">No activity found</p>
                    </div>
                </td>
            </tr>
        `;
    }

    // Render pagination
    if (data.pagination) {
        renderActivityPagination(data.pagination);
    }
}


// Thêm vào phần utility functions
function formatDateTime(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('vi-VN', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function getUserAvatarText(username) {
    if (!username) return '?';
    return username.substring(0, 2).toUpperCase();
}

function renderActivityPagination(pagination) {
    const element = document.getElementById('activity-pagination');
    if (!element) return;

    element.innerHTML = '';

    const totalPages = pagination.total_pages;
    const currentPage = pagination.page;

    // Previous button
    const prevButton = document.createElement('button');
    prevButton.className = 'page-btn';
    prevButton.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>';
    prevButton.disabled = currentPage === 1;
    prevButton.onclick = () => loadActivity(currentPage - 1);
    element.appendChild(prevButton);

    // Page numbers
    const startPage = Math.max(1, currentPage - 2);
    const endPage = Math.min(totalPages, startPage + 4);

    for (let i = startPage; i <= endPage; i++) {
        const pageButton = document.createElement('button');
        pageButton.className = `page-btn ${i === currentPage ? 'active' : ''}`;
        pageButton.textContent = i;
        pageButton.onclick = () => loadActivity(i);
        element.appendChild(pageButton);
    }

    // Next button
    const nextButton = document.createElement('button');
    nextButton.className = 'page-btn';
    nextButton.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>';
    nextButton.disabled = currentPage === totalPages;
    nextButton.onclick = () => loadActivity(currentPage + 1);
    element.appendChild(nextButton);

    // Page info
    const info = document.createElement('span');
    info.className = 'page-info';
    info.textContent = `Page ${currentPage} of ${totalPages}`;
    element.appendChild(info);

    // Total count
    const totalInfo = document.createElement('span');
    totalInfo.className = 'page-info';
    totalInfo.textContent = `(${pagination.total} activities)`;
    element.appendChild(totalInfo);
}

function createDailyActivityChart(dailyActivity) {
    const ctx = document.getElementById('dailyActivityChart').getContext('2d');

    if (chartInstances['dailyActivity']) {
        chartInstances['dailyActivity'].destroy();
    }

    const labels = dailyActivity.map(stat => stat.date).reverse();
    const sessions = dailyActivity.map(stat => stat.sessions).reverse();
    const activeUsers = dailyActivity.map(stat => stat.active_users || 0).reverse();

    chartInstances['dailyActivity'] = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Sessions',
                    data: sessions,
                    borderColor: '#4a6cf7',
                    backgroundColor: 'rgba(74, 108, 247, 0.1)',
                    borderWidth: 2,
                    yAxisID: 'y'
                },
                {
                    label: 'Active Users',
                    data: activeUsers,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 2,
                    borderDash: [5, 5],
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            interaction: {
                mode: 'index',
                intersect: false
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Sessions'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Active Users'
                    },
                    grid: {
                        drawOnChartArea: false
                    }
                }
            }
        }
    });
}


// Utility functions
function showLoading(tab) {
    document.getElementById(`${tab}-loading`).style.display = 'block';
    document.getElementById(`${tab}-content`).style.display = 'none';
}

function hideLoading(tab) {
    document.getElementById(`${tab}-loading`).style.display = 'none';
    document.getElementById(`${tab}-content`).style.display = 'block';
}

// Modal functions with animations
let activeModal = null;
let isOpeningModal = false; // Thêm flag để track trạng thái mở

function showModal(modalId) {
    if (isOpeningModal) return; // Ngăn mở nhiều lần

    const modal = document.getElementById(modalId);
    if (!modal) return;

    // Đánh dấu đang mở modal
    isOpeningModal = true;

    // Kiểm tra nếu đã có modal đang mở
    if (activeModal && activeModal !== modalId) {
        closeModal(activeModal);
    }

    activeModal = modalId;
    modal.style.display = 'flex';

    // Ngăn scroll body khi modal mở
    document.body.style.overflow = 'hidden';
    document.body.style.paddingRight = '15px'; // Bù cho scrollbar width

    // Animate in
    setTimeout(() => {
        modal.classList.add('show');
        isOpeningModal = false; // Reset flag
    }, 10);

    // Ngăn click event khi đang mở
    event?.stopPropagation();
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;

    // Animate out
    modal.classList.remove('show');

    setTimeout(() => {
        modal.style.display = 'none';

        // Reset modal content if needed
        switch (modalId) {
            case 'reset-password-modal':
                currentUserId = '';
                document.getElementById('reset-password-result').innerHTML = '';
                break;
            case 'user-detail-modal':
                // Không clear content để cache
                break;
        }

        // Reset active modal
        if (activeModal === modalId) {
            activeModal = null;
        }

        // Khôi phục scroll cho body
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
    }, 300);

    // Ngăn click event khi đang đóng
    event?.stopPropagation();
}

// Sửa lại click handler để ngăn sự kiện lan truyền
function setupModalOutsideClick() {
    document.querySelectorAll('.admin-modal').forEach(modal => {
        // Dùng capture phase để xử lý trước
        modal.addEventListener('click', function (event) {
            // Chỉ đóng khi click vào overlay (không phải modal content)
            // VÀ không phải đang trong quá trình mở/đóng
            if (event.target === this && !isOpeningModal) {
                closeModal(this.id);
                event.stopPropagation(); // Ngăn lan truyền
            }
        }, true); // Capture phase
    });
}

// Thêm hàm mở modal an toàn với event
function openModalSafely(modalId, event) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    showModal(modalId);
}

// Setup ESC key handler
document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape' && activeModal) {
        closeModal(activeModal);
    }
});



function resetFilters() {
    document.getElementById('user-search').value = '';
    loadUsers(1);
}

// Close modal when clicking outside
window.onclick = function (event) {
    const modals = document.querySelectorAll('.admin-modal');
    modals.forEach(modal => {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });
};

// Handle keyboard shortcuts
document.addEventListener('keydown', function (event) {
    // Escape key closes modals
    if (event.key === 'Escape') {
        document.querySelectorAll('.admin-modal').forEach(modal => {
            modal.style.display = 'none';
        });
    }
});

const modalStyle = document.createElement('style');
modalStyle.textContent = `
.admin-modal {
    transition: opacity 0.3s ease;
    opacity: 0;
}
.admin-modal .modal-content {
    transition: all 0.3s ease;
    transform: scale(0.95);
    opacity: 0;
}
`;
document.head.appendChild(modalStyle);

// Utility Functions
function getUserAvatarText(name) {
    if (!name) return '?';
    const names = name.split(' ');
    if (names.length >= 2) {
        return (names[0][0] + names[names.length - 1][0]).toUpperCase();
    }
    return name.substring(0, 2).toUpperCase();
}

function formatDateTime(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('vi-VN', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function createDailyActivityChart(dailyActivity) {
    const ctx = document.getElementById('dailyActivityChart').getContext('2d');

    if (chartInstances['dailyActivity']) {
        chartInstances['dailyActivity'].destroy();
    }

    const labels = dailyActivity.map(item => item.date);
    const sessions = dailyActivity.map(item => item.sessions);
    const minutes = dailyActivity.map(item => Math.round(item.minutes / 60 * 10) / 10);

    chartInstances['dailyActivity'] = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Sessions',
                    data: sessions,
                    borderColor: '#4a6cf7',
                    backgroundColor: 'rgba(74, 108, 247, 0.1)',
                    yAxisID: 'y'
                },
                {
                    label: 'Study Hours',
                    data: minutes,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: { display: true, text: 'Sessions' }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: { display: true, text: 'Hours' },
                    grid: { drawOnChartArea: false }
                }
            }
        }
    });
}

