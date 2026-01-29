<div class="admin-content">
    <!-- Tabs Navigation -->
    <!-- <div class="admin-tabs" id="adminTabs">
        <button class="tab-button active" data-tab="dashboard">📊 Dashboard</button>
        <button class="tab-button" data-tab="users">👥 Users</button>
        <button class="tab-button" data-tab="pomodoro">⏱️ Pomodoro Stats</button>
        <button class="tab-button" data-tab="activity">📈 Activity</button>
    </div> -->

    <!-- Dashboard Tab -->
    <div id="dashboard-tab" class="tab-content active">
        <div class="loading" id="dashboard-loading">
            <div class="loading-spinner"></div>
            <p>Loading dashboard...</p>
        </div>
        
        <div id="dashboard-content" style="display: none;">
            <!-- Stats Cards -->
            <div class="stats-grid" id="stats-grid"></div>
            
            <!-- Charts -->
            <div class="charts-grid">
                <div class="chart-container">
                    <h3 class="chart-title">Daily Study Time (Last 7 Days)</h3>
                    <canvas id="studyTimeChart"></canvas>
                </div>
                <div class="chart-container">
                    <h3 class="chart-title">User Activity</h3>
                    <canvas id="activityChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Tab -->
    <div id="users-tab" class="tab-content">
        <div class="admin-filters">
            <input type="text" 
                   id="user-search" 
                   class="form-control search-input" 
                   placeholder="Search users...">
            <button class="btn btn-primary" onclick="loadUsers()">Search</button>
            <button class="btn btn-secondary" onclick="resetFilters()">Reset</button>
        </div>
        
        <div class="loading" id="users-loading">
            <div class="loading-spinner"></div>
            <p>Loading users...</p>
        </div>
        
        <div id="users-content" style="display: none;">
            <div class="admin-table-container scroll overflow-x-auto">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="users-table-body"></tbody>
                </table>
            </div>
            
            <div class="admin-pagination" id="users-pagination"></div>
        </div>
    </div>

    <!-- Pomodoro Stats Tab -->
    <div id="pomodoro-tab" class="tab-content">
        <div class="admin-filters">
            <div class="date-range">
                <label>Last</label>
                <select id="pomodoro-days" class="form-control">
                    <option value="7">7 days</option>
                    <option value="30" selected>30 days</option>
                    <option value="90">90 days</option>
                    <option value="365">1 year</option>
                </select>
                <button class="btn btn-primary" onclick="loadPomodoroStats()">Update</button>
            </div>
        </div>
        
        <div class="loading" id="pomodoro-loading">
            <div class="loading-spinner"></div>
            <p>Loading pomodoro statistics...</p>
        </div>
        
        <div id="pomodoro-content" style="display: none;">
            <div class="stats-grid" id="pomodoro-stats"></div>
            
            <div class="chart-container mb-6">
                <h3 class="chart-title">Top Users by Study Time</h3>
                <canvas id="topUsersChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Activity Tab -->
    <div id="activity-tab" class="tab-content">
        <div class="admin-filters">
            <div class="date-range">
                <label>Last</label>
                <select id="activity-days" class="form-control">
                    <option value="7" selected>7 days</option>
                    <option value="30">30 days</option>
                    <option value="90">90 days</option>
                </select>
                <button class="btn btn-primary" onclick="loadActivity(1)">Update</button>
            </div>
        </div>
        
        <div class="loading" id="activity-loading">
            <div class="loading-spinner"></div>
            <p>Loading activity...</p>
        </div>
        
        <div id="activity-content" style="display: none;">
            <div class="chart-container mb-6">
                <h3 class="chart-title">Daily Activity Overview</h3>
                <canvas id="dailyActivityChart"></canvas>
            </div>
            
            <div class="admin-table-container">
                <h3 class="chart-title p-4">Recent Activity</h3>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Session</th>
                            <th>Duration</th>
                            <th>Topic</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody id="activity-table-body"></tbody>
                </table>
                
                <!-- THÊM PHẦN NÀY -->
                <div class="admin-pagination" id="activity-pagination"></div>
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
<div id="user-detail-modal" class="admin-modal">
    <div class="modal-content">
        
        <div class="modal-body" id="user-detail-content">
            <!-- Content sẽ được load dynamic -->
        </div>
    </div>
</div>

<div id="reset-password-modal" class="admin-modal">
    <div class="modal-content scroll">
        
        <div class="modal-body">
            <div class="mb-4">
                <p class="text-text">Are you sure you want to reset this user's password?</p>
                <p class="text-sm text-text-secondary mt-1">A new random password will be generated.</p>
            </div>
            <div id="reset-password-result" class="mb-4"></div>
            <div class="flex justify-end space-x-3">
                <button class="btn btn-secondary" onclick="closeModal('reset-password-modal')">Cancel</button>
                <button class="btn btn-danger" onclick="confirmResetPassword()">
                    Reset Password
                </button>
            </div>
        </div>
    </div>
</div>
