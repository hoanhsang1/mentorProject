/**
 * Pomodoro Timer Application
 */
class PomodoroApp {
    constructor() {
        this.timerInterval = null;
        this.remainingSeconds = 25 * 60; // Default: 25 minutes
        this.totalSeconds = 25 * 60;
        this.isRunning = false;
        this.currentSession = 'work'; // 'work' or 'break'
        this.pomodoroData = null;
        this.activeSession = null;
        this.autoStart = false;
        
        // Initialize
        this.init();
    }
    
    async init() {
        console.log('PomodoroApp initializing...');
        
        // Load initial data
        await this.loadPomodoroData();
        await this.loadStats();
        await this.loadHistory();
        
        // Setup event listeners
        this.setupEventListeners();
        
        // Update UI with loaded data
        this.updateUI();
        
        // Check for active session
        if (this.pomodoroData?.status === 'running') {
            console.log('Active session found, resuming...');
            this.resumeTimerFromSession();
        }
        
        console.log('PomodoroApp initialized');
    }
    
    async loadPomodoroData() {
        try {
            console.log('Loading pomodoro data from API...');
            
            const url = '/pomodoro/api/get';
            console.log('API URL:', url);
            
            const response = await fetch(url);
            console.log('Response status:', response.status, response.statusText);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const result = await response.json();
            console.log('API Response:', result);
            
            if (result.success) {
                console.log('Pomodoro data loaded successfully:', result.pomodoro);
                this.pomodoroData = result.pomodoro;
                this.updateTimerFromData();
            } else {
                console.error('API returned error:', result.error);
                App.showToast(result.error || 'Error loading pomodoro data', 'error');
            }
        } catch (error) {
            console.error('Error loading pomodoro data:', error);
            App.showToast('Error loading pomodoro data. Check console for details.', 'error');
        }
    }
    
    updateTimerFromData() {
        if (!this.pomodoroData) {
            console.log('No pomodoro data available');
            return;
        }
        
        console.log('Updating timer from data:', this.pomodoroData);
        
        // Update durations
        const workMinutes = this.pomodoroData.work_duration || 25;
        const breakMinutes = this.pomodoroData.break_duration || 5;
        
        this.currentSession = this.pomodoroData.current_session || 'work';
        
        if (this.currentSession === 'work') {
            this.totalSeconds = workMinutes * 60;
        } else {
            this.totalSeconds = breakMinutes * 60;
        }
        
        // For now, start fresh. In a real app, you'd calculate remaining time
        this.remainingSeconds = this.totalSeconds;
        
        // Update UI elements
        document.getElementById('workDuration').textContent = `${workMinutes} min`;
        document.getElementById('breakDuration').textContent = `${breakMinutes} min`;
        document.getElementById('sessionsCompleted').textContent = 
            this.pomodoroData.sessions_completed || 0;
        
        // Update timer label
        this.updateTimerLabel();
        this.updateTimerDisplay();
        
        // Update button states based on status
        this.updateButtonStates();
    }
    
    updateButtonStates() {
        if (!this.pomodoroData) return;
        
        const status = this.pomodoroData.status;
        const startBtn = document.getElementById('startBtn');
        const pauseBtn = document.getElementById('pauseBtn');
        const endBtn = document.getElementById('endBtn');
        const switchBtn = document.getElementById('switchBtn');
        const studyTopicGroup = document.getElementById('studyTopicGroup');
        
        console.log('Updating button states for status:', status);
        
        switch (status) {
            case 'stopped':
                startBtn.disabled = false;
                startBtn.innerHTML = '<span class="loading-spinner" style="display: none;"></span><svg width="20" height="20" viewBox="0 0 24 24" fill="none" class="mr-2"><path d="M8 5V19L19 12L8 5Z" fill="currentColor"/></svg>Start';
                // XÓA: startBtn.onclick = () => this.startSession();
                
                pauseBtn.disabled = true;
                endBtn.disabled = true;
                switchBtn.disabled = true;
                
                if (studyTopicGroup) studyTopicGroup.style.display = 'block';
                break;
                
            case 'running':
                startBtn.disabled = true;
                startBtn.innerHTML = '<span class="loading-spinner" style="display: none;"></span><svg width="20" height="20" viewBox="0 0 24 24" fill="none" class="mr-2"><path d="M8 5V19L19 12L8 5Z" fill="currentColor"/></svg>Start';
                
                pauseBtn.disabled = false;
                pauseBtn.innerHTML = '<span class="loading-spinner" style="display: none;"></span><svg width="20" height="20" viewBox="0 0 24 24" fill="none" class="mr-2"><path d="M6 19H10V5H6V19ZM14 5V19H18V5H14Z" fill="currentColor"/></svg>Pause';
                // XÓA: pauseBtn.onclick = () => this.pauseSession();
                
                endBtn.disabled = false;
                switchBtn.disabled = false;
                
                if (studyTopicGroup) studyTopicGroup.style.display = 'none';
                break;
                
            case 'paused':
                startBtn.disabled = false;
                startBtn.innerHTML = '<span class="loading-spinner" style="display: none;"></span><svg width="20" height="20" viewBox="0 0 24 24" fill="none" class="mr-2"><path d="M8 5V19L19 12L8 5Z" fill="currentColor"/></svg>Resume';
                // XÓA: startBtn.onclick = () => this.resumeSession();
                
                pauseBtn.disabled = true;
                endBtn.disabled = false;
                switchBtn.disabled = true;
                
                if (studyTopicGroup) studyTopicGroup.style.display = 'none';
                break;
        }
    }
    
    async loadStats() {
        try {
            console.log('Loading stats...');
            const response = await fetch('/pomodoro/api/stats?days=7');
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const result = await response.json();
            console.log('Stats loaded:', result);
            
            if (result.success) {
                this.updateStatsUI(result);
            }
        } catch (error) {
            console.error('Error loading stats:', error);
        }
    }
    
    updateStatsUI(data) {
        if (!data || !data.summary) {
            console.log('No stats data available');
            return;
        }
        
        console.log('Updating stats UI:', data);
        
        const totalSessions = data.summary.total_sessions || 0;
        const totalMinutes = data.summary.total_minutes || 0;
        const totalHours = data.summary.total_hours || 0;
        
        // SAFE UPDATE - kiểm tra element tồn tại trước khi update
        this.safeUpdateText('totalSessions', totalSessions);
        this.safeUpdateText('totalMinutes', totalMinutes);
        this.safeUpdateText('totalHours', totalHours.toFixed(1));
        
        // Calculate today's sessions
        const today = new Date().toISOString().split('T')[0];
        const todayStats = data.stats?.find(stat => stat.session_date === today);
        this.safeUpdateText('todaySessions', todayStats?.total_sessions || 0);
        
        // Calculate average duration
        const avgDuration = totalSessions > 0 
            ? Math.round(totalMinutes / totalSessions)
            : 0;
        this.safeUpdateText('avgDuration', `${avgDuration} min`);
    }

    safeUpdateText(elementId, text) {
        const element = document.getElementById(elementId);
        if (element) {
            element.textContent = text;
        } else {
            console.warn(`Element #${elementId} not found`);
        }
    }
    
    async loadHistory() {
        try {
            console.log('Loading history...');
            const response = await fetch('/pomodoro/api/history?limit=10');
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const result = await response.json();
            console.log('History API Response:', result);
            
            if (result.success) {
                console.log('History data received:', result.history);
                console.log('Type:', typeof result.history);
                console.log('Is array?', Array.isArray(result.history));
                console.log('Length:', result.history?.length || 0);
                
                if (result.history && result.history.length > 0) {
                    console.log('First session details:', result.history[0]);
                    console.log('Session status:', result.history[0].status);
                    console.log('Session study_topic:', result.history[0].study_topic);
                }
                
                const historyData = result.history || [];
                this.updateHistoryUI(historyData);
            } else {
                console.warn('API returned error for history:', result.error);
            }
        } catch (error) {
            console.error('Error loading history:', error);
        }
    }
    
    updateHistoryUI(history) {
        const tbody = document.getElementById('historyBody');
        
        if (!tbody) {
            console.error('History table body not found');
            return;
        }
        
        if (!history || history.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="4" class="p-8 text-center text-text-secondary">
                        <div class="empty-state">
                            <div class="empty-state-icon">📊</div>
                            <div class="empty-state-text">No sessions yet</div>
                            <div class="empty-state-subtext">Start your first Pomodoro session!</div>
                        </div>
                    </td>
                </tr>
            `;
            return;
        }
        
        tbody.innerHTML = history.map(session => `
            <tr class="border-b border-border hover:bg-bg-input">
                <td class="p-4">${this.escapeHtml(session.study_topic || 'No topic')}</td>
                <td class="p-4">${session.duration_minutes || 0} min</td>
                <td class="p-4">
                    <span class="status-badge status-${session.status}">
                        ${session.status.replace('_', ' ')}
                    </span>
                </td>
                <td class="p-4">${this.formatDate(session.start_time)}</td>
            </tr>
        `).join('');
    }
    
    async startSession() {
        const studyTopic = document.getElementById('studyTopic')?.value || '';
        
        try {
            console.log('Starting session with topic:', studyTopic);
            
            // Show loading state
            const startBtn = document.getElementById('startBtn');
            const originalText = startBtn.innerHTML;
            startBtn.innerHTML = '<span class="loading-spinner"></span>Starting...';
            startBtn.disabled = true;
            
            const formData = new FormData();
            if (studyTopic.trim()) {
                formData.append('study_topic', studyTopic.trim());
            }
            
            const response = await fetch('/pomodoro/api/start', {
                method: 'POST',
                body: formData
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const result = await response.json();
            console.log('Start session response:', result);
            
            // Restore button
            startBtn.innerHTML = originalText;
            startBtn.disabled = false;
            
            if (result.success) {
                this.pomodoroData = result.pomodoro;
                this.activeSession = result.session;
                this.isRunning = true;
                this.resetTimer();
                this.updateUI();
                this.startTimer();
                App.showToast('Pomodoro session started! Focus time! 🎯', 'success');
                
                // Hide study topic input
                const studyTopicGroup = document.getElementById('studyTopicGroup');
                if (studyTopicGroup) studyTopicGroup.style.display = 'none';
            } else {
                App.showToast(result.error || 'Failed to start session', 'error');
            }
        } catch (error) {
            console.error('Error starting session:', error);
            App.showToast('Error starting session', 'error');
            
            // Restore button
            const startBtn = document.getElementById('startBtn');
            startBtn.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" class="mr-2"><path d="M8 5V19L19 12L8 5Z" fill="currentColor"/></svg>Start';
            startBtn.disabled = false;
        }
    }
    
    async pauseSession() {
        try {
            console.log('Pausing session...');
            
            // Show loading state
            const pauseBtn = document.getElementById('pauseBtn');
            const originalText = pauseBtn.innerHTML;
            pauseBtn.innerHTML = '<span class="loading-spinner"></span>Pausing...';
            pauseBtn.disabled = true;
            
            const response = await fetch('/pomodoro/api/pause', {
                method: 'POST'
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const result = await response.json();
            console.log('Pause session response:', result);
            
            // Restore button
            pauseBtn.innerHTML = originalText;
            pauseBtn.disabled = false;
            
            if (result.success) {
                this.pomodoroData = result.pomodoro;
                this.isRunning = false;
                this.updateUI();
                this.stopTimer();
                App.showToast('Session paused ⏸️', 'info');
            } else {
                App.showToast(result.error || 'Failed to pause session', 'error');
            }
        } catch (error) {
            console.error('Error pausing session:', error);
            App.showToast('Error pausing session', 'error');
            
            // Restore button
            const pauseBtn = document.getElementById('pauseBtn');
            pauseBtn.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" class="mr-2"><path d="M6 19H10V5H6V19ZM14 5V19H18V5H14Z" fill="currentColor"/></svg>Pause';
            pauseBtn.disabled = false;
        }
    }
    
    async resumeSession() {
        try {
            console.log('Resuming session...');
            
            // Show loading state
            const startBtn = document.getElementById('startBtn');
            const originalText = startBtn.innerHTML;
            startBtn.innerHTML = '<span class="loading-spinner"></span>Resuming...';
            startBtn.disabled = true;
            
            const response = await fetch('/pomodoro/api/resume', {
                method: 'POST'
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const result = await response.json();
            console.log('Resume session response:', result);
            
            // Restore button
            startBtn.innerHTML = originalText;
            startBtn.disabled = false;
            
            if (result.success) {
                this.pomodoroData = result.pomodoro;
                this.isRunning = true;
                this.updateUI();
                this.startTimer();
                App.showToast('Session resumed! Keep going! 💪', 'success');
            } else {
                App.showToast(result.error || 'Failed to resume session', 'error');
            }
        } catch (error) {
            console.error('Error resuming session:', error);
            App.showToast('Error resuming session', 'error');
            
            // Restore button
            const startBtn = document.getElementById('startBtn');
            startBtn.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" class="mr-2"><path d="M8 5V19L19 12L8 5Z" fill="currentColor"/></svg>Resume';
            startBtn.disabled = false;
        }
    }
    
    /**
     * Reset timer về giá trị ban đầu
     */
    resetTimer() {
        if (!this.pomodoroData) return;
        
        const workMinutes = this.pomodoroData.work_duration || 25;
        const breakMinutes = this.pomodoroData.break_duration || 5;
        
        this.currentSession = this.pomodoroData.current_session || 'work';
        
        if (this.currentSession === 'work') {
            this.totalSeconds = workMinutes * 60;
        } else {
            this.totalSeconds = breakMinutes * 60;
        }
        
        this.remainingSeconds = this.totalSeconds;
        this.updateTimerDisplay();
        
        console.log('Timer reset to:', {
            session: this.currentSession,
            totalSeconds: this.totalSeconds,
            display: `${Math.floor(this.totalSeconds / 60)}:${this.totalSeconds % 60}`
        });
    }

    async endSession(completed = true) {
        try {
            console.log('Ending session, completed:', completed);
            
            // Show loading state
            const endBtn = document.getElementById('endBtn');
            const originalText = endBtn.innerHTML;
            endBtn.innerHTML = '<span class="loading-spinner"></span>Ending...';
            endBtn.disabled = true;
            
            const formData = new FormData();
            formData.append('completed', completed.toString());
            
            const response = await fetch('/pomodoro/api/end', {
                method: 'POST',
                body: formData
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const result = await response.json();
            console.log('End session response:', result);
            
            // Restore button
            endBtn.innerHTML = originalText;
            endBtn.disabled = false;
            
            if (result.success) {
                this.pomodoroData = result.pomodoro;
                this.isRunning = false;
                this.activeSession = null;
                this.resetTimer()
                this.updateUI();
                this.stopTimer();
                
                // Show study topic input again
                const studyTopicGroup = document.getElementById('studyTopicGroup');
                if (studyTopicGroup) {
                    studyTopicGroup.style.display = 'block';
                    document.getElementById('studyTopic').value = '';
                }
                
                App.showToast(
                    completed ? 'Session completed! Great work! 🎉' : 'Session cancelled', 
                    completed ? 'success' : 'info'
                );
                
                // Reload history and stats
                await this.loadHistory();
                await this.loadStats();
            } else {
                App.showToast(result.error || 'Failed to end session', 'error');
            }
        } catch (error) {
            console.error('Error ending session:', error);
            App.showToast('Error ending session', 'error');
            
            // Restore button
            const endBtn = document.getElementById('endBtn');
            endBtn.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" class="mr-2"><path d="M6 6H18V18H6V6Z" fill="currentColor"/></svg>End';
            endBtn.disabled = false;
        }
    }
    
    async switchSession() {
        try {
            console.log('Switching session...');
            
            // Show loading state
            const switchBtn = document.getElementById('switchBtn');
            const originalText = switchBtn.innerHTML;
            switchBtn.innerHTML = '<span class="loading-spinner"></span>Switching...';
            switchBtn.disabled = true;
            
            const response = await fetch('/pomodoro/api/switch', {
                method: 'POST'
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const result = await response.json();
            console.log('Switch session response:', result);
            
            // Restore button
            switchBtn.innerHTML = originalText;
            switchBtn.disabled = false;
            
            if (result.success) {
                this.pomodoroData = result.pomodoro;
                this.currentSession = result.new_session;
                
                // Reset timer for new session
                const duration = this.currentSession === 'work' 
                    ? this.pomodoroData.work_duration 
                    : this.pomodoroData.break_duration;
                
                this.totalSeconds = duration * 60;
                this.remainingSeconds = this.totalSeconds;
                
                this.updateUI();
                this.updateTimerDisplay();
                App.showToast(`Switched to ${this.currentSession} session`, 'info');
                
                // Play sound
                this.playNotificationSound();
                
                // Auto-start next session if enabled
                if (this.autoStart && this.isRunning) {
                    this.startTimer();
                }
            } else {
                App.showToast(result.error || 'Failed to switch session', 'error');
            }
        } catch (error) {
            console.error('Error switching session:', error);
            App.showToast('Error switching session', 'error');
            
            // Restore button
            const switchBtn = document.getElementById('switchBtn');
            switchBtn.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" class="mr-2"><path d="M12 4V20M20 12H4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>Switch';
            switchBtn.disabled = false;
        }
    }
    
    async updateSettings(workDuration, breakDuration) {
        try {
            console.log('Updating settings:', { workDuration, breakDuration });
            
            const formData = new FormData();
            formData.append('work_duration', workDuration);
            formData.append('break_duration', breakDuration);
            
            const response = await fetch('/pomodoro/api/update-settings', {
                method: 'POST',
                body: formData
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const result = await response.json();
            console.log('Update settings response:', result);
            
            if (result.success) {
                this.pomodoroData = result.pomodoro;
                this.updateUI();
                App.showToast('Settings updated successfully! ⚙️', 'success');
                this.closeSettingsModal();
            } else {
                App.showToast(result.error || 'Failed to update settings', 'error');
            }
        } catch (error) {
            console.error('Error updating settings:', error);
            App.showToast('Error updating settings', 'error');
        }
    }
    
    startTimer() {
        this.stopTimer(); // Clear any existing timer
        
        console.log('Starting timer...');
        this.timerInterval = setInterval(() => {
            this.remainingSeconds--;
            
            if (this.remainingSeconds <= 0) {
                this.onTimerComplete();
                return;
            }
            
            this.updateTimerDisplay();
        }, 1000);
    }
    
    resumeTimerFromSession() {
        console.log('Resuming timer from session...');
        this.isRunning = true;
        this.updateUI();
        this.startTimer();
    }
    
    stopTimer() {
        if (this.timerInterval) {
            console.log('Stopping timer...');
            clearInterval(this.timerInterval);
            this.timerInterval = null;
        }
    }
    
    onTimerComplete() {
        console.log('Timer completed!');
        this.stopTimer();
        
        // Play sound
        this.playNotificationSound();
        
        // Show notification
        const sessionType = this.currentSession === 'work' ? 'Work' : 'Break';
        App.showToast(`${sessionType} session completed! 🎉`, 'success');
        
        // Auto-switch if enabled
        if (this.autoStart) {
            console.log('Auto-switching session...');
            this.switchSession();
        } else {
            // Just update UI
            this.remainingSeconds = 0;
            this.updateTimerDisplay();
        }
    }
    
    updateTimerDisplay() {
        const minutes = Math.floor(this.remainingSeconds / 60);
        const seconds = this.remainingSeconds % 60;
        
        // Update time display
        const timerTime = document.getElementById('timerTime');
        if (timerTime) {
            timerTime.textContent = 
                `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        }
        
        // Update progress bar
        const progress = ((this.totalSeconds - this.remainingSeconds) / this.totalSeconds) * 100;
        const timerProgress = document.getElementById('timerProgress');
        if (timerProgress) {
            timerProgress.style.width = `${progress}%`;
        }
        
        // Update timer label
        this.updateTimerLabel();
    }
    
    updateTimerLabel() {
        const label = this.currentSession === 'work' ? 'Work Session' : 'Break Session';
        const emoji = this.currentSession === 'work' ? '💼' : '☕';
        const timerLabel = document.getElementById('timerLabel');
        if (timerLabel) {
            timerLabel.textContent = `${emoji} ${label}`;
        }
    }
    
    updateUI() {
        this.updateButtonStates();
        this.updateTimerDisplay();
        this.updateTimerLabel();
    }
    
    setupEventListeners() {
        console.log('Setting up event listeners...');
        
        // Start button - Xử lý cả Start và Resume
        const startBtn = document.getElementById('startBtn');
        if (startBtn) {
            startBtn.addEventListener('click', () => {
                if (!this.pomodoroData) return;
                
                if (this.pomodoroData.status === 'paused') {
                    this.resumeSession();
                } else if (this.pomodoroData.status === 'stopped') {
                    this.startSession();
                }
            });
        }
        
        // Pause button
        const pauseBtn = document.getElementById('pauseBtn');
        if (pauseBtn) {
            pauseBtn.addEventListener('click', () => {
                this.pauseSession();
            });
        }
        
        // End button - SIMPLIFIED
        const endBtn = document.getElementById('endBtn');
        if (endBtn) {
            endBtn.addEventListener('click', () => {
                this.endSession(true); // Always mark as completed
            });
        }
        
        // Switch button
        const switchBtn = document.getElementById('switchBtn');
        if (switchBtn) {
            switchBtn.addEventListener('click', () => {
                this.switchSession();
            });
        }
        
        // Refresh history
        const refreshHistoryBtn = document.getElementById('refreshHistoryBtn');
        if (refreshHistoryBtn) {
            refreshHistoryBtn.addEventListener('click', async () => {
                refreshHistoryBtn.disabled = true;
                const originalText = refreshHistoryBtn.innerHTML;
                refreshHistoryBtn.innerHTML = '<span class="loading-spinner"></span>Refreshing...';
                
                await this.loadHistory();
                await this.loadStats();
                
                refreshHistoryBtn.innerHTML = originalText;
                refreshHistoryBtn.disabled = false;
                App.showToast('History refreshed 🔄', 'success');
            });
        }
        
        // Settings modal
        const settingsBtn = document.getElementById('settingsBtn');
        if (settingsBtn) {
            settingsBtn.addEventListener('click', () => {
                this.openSettingsModal();
            });
        }
        
        // Modal close buttons
        const closeButtons = ['closeSettingsBtn', 'cancelSettingsBtn'];
        closeButtons.forEach(id => {
            const btn = document.getElementById(id);
            if (btn) {
                btn.addEventListener('click', () => this.closeSettingsModal());
            }
        });
        
        // Save settings
        const saveSettingsBtn = document.getElementById('saveSettingsBtn');
        if (saveSettingsBtn) {
            saveSettingsBtn.addEventListener('click', () => {
                this.saveSettings();
            });
        }
        
        // Study topic enter key
        const studyTopicInput = document.getElementById('studyTopic');
        if (studyTopicInput) {
            studyTopicInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter' && !this.isRunning) {
                    e.preventDefault();
                    this.startSession();
                }
            });
        }
        
        // Close modal on overlay click
        const modalOverlay = document.getElementById('settingsModal');
        if (modalOverlay) {
            modalOverlay.addEventListener('click', (e) => {
                if (e.target === modalOverlay) {
                    this.closeSettingsModal();
                }
            });
        }
        
        // Escape key to close modal
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.closeSettingsModal();
            }
        });
    }
    
    openSettingsModal() {
        if (!this.pomodoroData) {
            App.showToast('Please wait for data to load', 'error');
            return;
        }
        
        console.log('Opening settings modal...');
        
        document.getElementById('settingsWorkDuration').value = this.pomodoroData.work_duration || 25;
        document.getElementById('settingsBreakDuration').value = this.pomodoroData.break_duration || 5;
        document.getElementById('autoStart').checked = this.autoStart;
        
        const modal = document.getElementById('settingsModal');
        if (modal) {
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }
    
    closeSettingsModal() {
        console.log('Closing settings modal...');
        
        const modal = document.getElementById('settingsModal');
        if (modal) {
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }
    }
    
    saveSettings() {
        const workDuration = parseInt(document.getElementById('settingsWorkDuration').value);
        const breakDuration = parseInt(document.getElementById('settingsBreakDuration').value);
        this.autoStart = document.getElementById('autoStart').checked;
        
        if (isNaN(workDuration) || isNaN(breakDuration) || workDuration < 1 || workDuration > 60 || breakDuration < 1 || breakDuration > 30) {
            App.showToast('Please enter valid durations (1-60 min work, 1-30 min break)', 'error');
            return;
        }
        
        this.updateSettings(workDuration, breakDuration);
    }
    
    playNotificationSound() {
        try {
            // Thử dùng Web Audio API để tạo âm thanh
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();
            
            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);
            
            // Cài đặt âm thanh notification
            oscillator.frequency.value = 800;
            oscillator.type = 'sine';
            
            gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.5);
            
            oscillator.start(audioContext.currentTime);
            oscillator.stop(audioContext.currentTime + 0.5);
            
            // Hiển thị visual notification
            this.showVisualNotification('⏰ Timer Complete!');
            
        } catch (e) {
            console.log('Web Audio API not supported:', e);
            // Fallback: sử dụng file âm thanh nếu có
            try {
                const audio = new Audio('/assets/sounds/notification.mp3');
                audio.play().catch(err => {
                    console.log('Audio file play failed:', err);
                    this.showVisualNotification('⏰ Timer Complete!');
                });
            } catch (err) {
                console.log('Audio fallback failed:', err);
                this.showVisualNotification('⏰ Timer Complete!');
            }
        }
    }
    
    showVisualNotification(message) {
        // Tạo notification visual
        const notification = document.createElement('div');
        notification.id = 'pomodoro-notification';
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #4a6cf7 0%, #3a5bd9 100%);
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 12px;
            z-index: 10000;
            box-shadow: 0 10px 25px -5px rgba(74, 108, 247, 0.3);
            animation: slideIn 0.3s ease-out;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        `;
        notification.innerHTML = `<span>${message}</span>`;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease-out';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
        
        // Thêm CSS animation nếu chưa có
        if (!document.querySelector('#pomodoro-notification-styles')) {
            const style = document.createElement('style');
            style.id = 'pomodoro-notification-styles';
            style.textContent = `
                @keyframes slideIn {
                    from { transform: translateX(100%); opacity: 0; }
                    to { transform: translateX(0); opacity: 1; }
                }
                @keyframes slideOut {
                    from { transform: translateX(0); opacity: 1; }
                    to { transform: translateX(100%); opacity: 0; }
                }
            `;
            document.head.appendChild(style);
        }
    }
    
    formatDate(dateString) {
        try {
            const date = new Date(dateString);
            const now = new Date();
            const diffMs = now - date;
            const diffMins = Math.floor(diffMs / 60000);
            const diffHours = Math.floor(diffMs / 3600000);
            const diffDays = Math.floor(diffMs / 86400000);
            
            if (diffMins < 1) return 'Just now';
            if (diffMins < 60) return `${diffMins}m ago`;
            if (diffHours < 24) return `${diffHours}h ago`;
            if (diffDays < 7) return `${diffDays}d ago`;
            
            return date.toLocaleDateString('en-US', {
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        } catch (e) {
            return dateString;
        }
    }
    
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM loaded, initializing PomodoroApp...');
    window.pomodoroApp = new PomodoroApp();
});

// Add CSS for pulse animation
const style = document.createElement('style');
style.textContent = `
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}
`;
document.head.appendChild(style);