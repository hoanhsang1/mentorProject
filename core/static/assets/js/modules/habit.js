// Habit Tracker JavaScript - Weekly View Only
class ModernHabitTracker {
    constructor() {
        this.currentMonth = new Date().getMonth() + 1;
        this.currentYear = new Date().getFullYear();
        this.currentWeek = 0;
        this.weeksInMonth = 0;
        this.selectedColor = '#4a6cf7';
        this.habits = [];
        this.lastData = null;
        this.deletingHabitId = null;
        this.deletingHabitTitle = '';
        
        this.isHabitPage = !!document.getElementById('habits-container');
        
        if (this.isHabitPage) {
            this.init();
        }
    }
    
    init() {
        this.bindEvents();
        this.calculateWeeksInMonth();
        this.loadHabits();
        this.updateMonthDisplay();
        this.updateWeekDisplay();
        this.setupColorPicker();
        this.setupEditModal();
    }
    setupEditModal() {
        // Edit color options
        document.querySelectorAll('.edit-color-option').forEach(option => {
            option.addEventListener('click', (e) => {
                this.selectEditColor(e.target.dataset.color);
            });
        });
        
        // Daily target controls
        const decreaseBtn = document.getElementById('decrease-target');
        const increaseBtn = document.getElementById('increase-target');
        const targetInput = document.getElementById('edit-daily-target');
        
        if (decreaseBtn) {
            decreaseBtn.addEventListener('click', () => {
                let value = parseInt(targetInput.value) || 1;
                if (value > 1) {
                    targetInput.value = value - 1;
                }
            });
        }
        
        if (increaseBtn) {
            increaseBtn.addEventListener('click', () => {
                let value = parseInt(targetInput.value) || 1;
                if (value < 10) {
                    targetInput.value = value + 1;
                }
            });
        }
        
        // Form submission
        const editForm = document.getElementById('edit-habit-form');
        if (editForm) {
            editForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.saveHabitChanges();
            });
        }
        
        // Close buttons
        const closeBtn = document.getElementById('close-edit-modal');
        const cancelBtn = document.getElementById('cancel-edit');
        
        if (closeBtn) {
            closeBtn.addEventListener('click', () => this.closeEditModal());
        }
        
        if (cancelBtn) {
            cancelBtn.addEventListener('click', () => this.closeEditModal());
        }
        
        // Close on backdrop click
        document.addEventListener('click', (e) => {
            if (e.target.id === 'edit-modal' || e.target.classList.contains('modal-backdrop')) {
                this.closeEditModal();
            }
        });
    }
    
    selectEditColor(color) {
        this.editingHabitColor = color;
        document.querySelectorAll('.edit-color-option').forEach(opt => {
            opt.classList.toggle('active', opt.dataset.color === color);
        });
    }
    
    showEditModal(habitId, habitTitle, habitColor = '#4a6cf7', dailyTarget = 1) {
        this.editingHabitId = habitId;
        this.editingHabitTitle = habitTitle;
        this.editingHabitColor = habitColor;
        this.editingHabitTarget = dailyTarget;
        
        const modal = document.getElementById('edit-modal');
        const nameInput = document.getElementById('edit-habit-name');
        const habitIdInput = document.getElementById('edit-habit-id');
        const targetInput = document.getElementById('edit-daily-target');
        
        if (!modal || !nameInput || !habitIdInput || !targetInput) return;
        
        // Set form values
        nameInput.value = habitTitle;
        habitIdInput.value = habitId;
        targetInput.value = dailyTarget;
        
        // Select the correct color
        this.selectEditColor(habitColor);
        
        // Show modal
        modal.style.display = 'flex';
        document.body.classList.add('modal-open');
        
        // Focus on name input
        setTimeout(() => {
            nameInput.focus();
            nameInput.select();
        }, 100);
    }

    closeEditModal() {
        const modal = document.getElementById('edit-modal');
        if (modal) {
            modal.style.display = 'none';
        }
        
        document.body.classList.remove('modal-open');
        this.editingHabitId = null;
        this.editingHabitTitle = '';
        this.editingHabitColor = '#4a6cf7';
        this.editingHabitTarget = 1;
        
        // Reset form
        const editForm = document.getElementById('edit-habit-form');
        if (editForm) {
            editForm.reset();
        }
    }
    
    async saveHabitChanges() {
        const nameInput = document.getElementById('edit-habit-name');
        const targetInput = document.getElementById('edit-daily-target');
        
        if (!nameInput || !targetInput || !this.editingHabitId) return;
        
        const newTitle = nameInput.value.trim();
        const newTarget = parseInt(targetInput.value) || 1;
        
        if (!newTitle) {
            App.showToast('Please enter a habit name', 'error');
            nameInput.focus();
            return;
        }
        
        if (newTitle.length > 50) {
            App.showToast('Habit name is too long (max 50 characters)', 'error');
            return;
        }
        
        try {
            const saveBtn = document.getElementById('save-habit');
            if (saveBtn) {
                saveBtn.classList.add('loading');
                saveBtn.disabled = true;
            }
            
            const response = await fetch('/habit/api/update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    habit_id: this.editingHabitId,
                    title: newTitle,
                    color: this.editingHabitColor,
                    daily_target: newTarget
                })
            });
            
            if (saveBtn) {
                saveBtn.classList.remove('loading');
                saveBtn.disabled = false;
            }
            
            if (!response.ok) throw new Error('Update failed');
            
            const data = await response.json();
            
            if (data.success) {
                App.showToast('Habit updated successfully!', 'success');
                this.closeEditModal();
                await this.loadHabits();
            } else {
                App.showToast(data.error || 'Failed to update habit', 'error');
            }
        } catch (error) {
            console.error('Error updating habit:', error);
            App.showToast('Error updating habit', 'error');
            const saveBtn = document.getElementById('save-habit');
            if (saveBtn) {
                saveBtn.classList.remove('loading');
                saveBtn.disabled = false;
            }
        }
    }
    bindEvents() {
        // Month navigation
        const prevMonthBtn = document.getElementById('prev-month');
        const nextMonthBtn = document.getElementById('next-month');
        const todayBtn = document.getElementById('today-btn');
        
        if (prevMonthBtn) {
            prevMonthBtn.addEventListener('click', () => this.prevMonth());
        }
        
        if (nextMonthBtn) {
            nextMonthBtn.addEventListener('click', () => this.nextMonth());
        }
        
        if (todayBtn) {
            todayBtn.addEventListener('click', () => this.goToToday());
        }
        
        // Week navigation
        const prevWeekBtn = document.getElementById('prev-week');
        const nextWeekBtn = document.getElementById('next-week');
        
        if (prevWeekBtn) {
            prevWeekBtn.addEventListener('click', () => this.prevWeek());
        }
        
        if (nextWeekBtn) {
            nextWeekBtn.addEventListener('click', () => this.nextWeek());
        }
        
        // Add habit
        const addHabitBtn = document.getElementById('add-habit-btn');
        const newHabitInput = document.getElementById('new-habit-input');
        
        if (addHabitBtn) {
            addHabitBtn.addEventListener('click', () => this.addHabit());
        }
        
        if (newHabitInput) {
            newHabitInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') this.addHabit();
            });
        }
        
        // Modal events
        const cancelDeleteBtn = document.getElementById('cancel-delete');
        const confirmDeleteBtn = document.getElementById('confirm-delete');
        
        if (cancelDeleteBtn) {
            cancelDeleteBtn.addEventListener('click', () => this.closeModal());
        }
        
        if (confirmDeleteBtn) {
            confirmDeleteBtn.addEventListener('click', () => this.deleteHabitConfirmed());
        }
        
        document.addEventListener('click', (e) => {
            // Close confirm modal
            if (e.target.id === 'confirm-modal' || e.target.classList.contains('modal-backdrop')) {
                this.closeModal();
            }
            
            // Close edit modal
            if (e.target.id === 'edit-modal' || e.target.classList.contains('modal-backdrop')) {
                this.closeEditModal();
            }
        });
        
        // Close modal on ESC
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.closeModal();
                this.closeEditModal();
            }
        });
    }
    
    setupColorPicker() {
        document.querySelectorAll('.color-option').forEach(option => {
            option.addEventListener('click', (e) => {
                this.selectColor(e.target.dataset.color);
            });
        });
    }
    
    selectColor(color) {
        this.selectedColor = color;
        document.querySelectorAll('.color-option').forEach(opt => {
            opt.classList.toggle('active', opt.dataset.color === color);
        });
    }
    
    async loadHabits() {
        try {
            const container = document.getElementById('habits-container');
            if (container) container.classList.add('loading');
            
            const response = await fetch(`/habit/api/get?month=${this.currentMonth}&year=${this.currentYear}`);
            
            if (!response.ok) throw new Error('Failed to load habits');
            
            const data = await response.json();
            
            if (container) container.classList.remove('loading');
            
            if (data.success) {
                this.habits = data.habits || [];
                this.lastData = data;
                
                this.renderWeeklyView(data);
                this.updateStats();
            } else {
                App.showToast(data.error || 'Failed to load habits', 'error');
            }
        } catch (error) {
            console.error('Error loading habits:', error);
            App.showToast('Error loading habits', 'error');
            
            const container = document.getElementById('habits-container');
            if (container) container.classList.remove('loading');
        }
    }
    
    // ==================== WEEKLY VIEW ====================
    
    calculateWeeksInMonth() {
        const firstDay = new Date(this.currentYear, this.currentMonth - 1, 1);
        const lastDay = new Date(this.currentYear, this.currentMonth, 0);
        const daysInMonth = lastDay.getDate();
        
        const today = new Date();
        if (this.currentMonth === today.getMonth() + 1 && this.currentYear === today.getFullYear()) {
            const firstDayOfWeek = firstDay.getDay();
            const adjustedFirstDay = (firstDayOfWeek + 6) % 7;
            this.currentWeek = Math.floor((today.getDate() + adjustedFirstDay - 1) / 7);
        } else {
            this.currentWeek = 0;
        }
        
        this.weeksInMonth = Math.ceil((daysInMonth + firstDay.getDay()) / 7);
    }
    
    getWeekDates() {
        // Tạo ngày đầu tiên của tuần hiện tại
        // Tuần 0: 1-7, Tuần 1: 8-14, Tuần 2: 15-21, Tuần 3: 22-28, Tuần 4: 29-31
        const weekStartDay = (this.currentWeek * 7) + 1;
        
        // Tạo date object
        const weekStartDate = new Date(this.currentYear, this.currentMonth - 1, weekStartDay);
        
        // Điều chỉnh để bắt đầu từ thứ 2
        // getDay(): 0=CN, 1=T2, 2=T3, 3=T4, 4=T5, 5=T6, 6=T7
        const currentDayOfWeek = weekStartDate.getDay();
        const daysToMonday = currentDayOfWeek === 0 ? -6 : 1 - currentDayOfWeek;
        
        weekStartDate.setDate(weekStartDate.getDate() + daysToMonday);
        
        console.log('=== FINAL WEEK CALC ===');
        console.log('Week:', this.currentWeek, 'Start day calc:', (this.currentWeek * 7) + 1);
        console.log('Initial date:', new Date(this.currentYear, this.currentMonth - 1, (this.currentWeek * 7) + 1).toDateString());
        console.log('Day of week:', currentDayOfWeek, '(0=Sun)');
        console.log('Days to Monday:', daysToMonday);
        console.log('Final start date:', weekStartDate.toDateString());
        
        const weekDates = [];
        for (let i = 0; i < 7; i++) {
            const date = new Date(weekStartDate);
            date.setDate(date.getDate() + i);
            weekDates.push(date);
            
            console.log(`Day ${i}: ${date.getDate()}/${date.getMonth()+1}/${date.getFullYear()} (${date.toDateString()})`);
        }
        
        return weekDates;
    }
    
    renderWeeklyView(data) {
        const container = document.getElementById('habits-container');
        const weekHeader = document.getElementById('week-header');
        
        if (!container) return;
        
        if (!this.habits || this.habits.length === 0) {
            container.innerHTML = this.getEmptyStateHTML();
            if (weekHeader) weekHeader.innerHTML = '';
            return;
        }
        
        const weekDates = this.getWeekDates();
        
        console.log('=== RENDER DEBUG ===');
        console.log('Week dates:', weekDates.map(d => `${d.getDate()}/${d.getMonth()+1}`));
        console.log('Current week:', this.currentWeek);
        console.log('Current month:', this.currentMonth);
        console.log('Current year:', this.currentYear);
        
        // Update week header
        if (weekHeader) {
            weekHeader.innerHTML = `
                <div class="week-header-grid">
                    <div class="habit-name-header">Habits</div>
                    ${weekDates.map((date, index) => {
                        const dayName = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'][(date.getDay() + 6) % 7];
                        const dayNum = date.getDate();
                        const month = date.getMonth() + 1;
                        const isToday = this.isToday(date);
                        
                        console.log(`Header ${index}: ${dayName} ${dayNum}/${month} - Today: ${isToday}`);
                        
                        return `
                            <div class="day-header-week" data-debug="${dayNum}/${month}">
                                <div class="text-xs font-medium text-text-secondary">
                                    ${dayName}
                                </div>
                                <div class="text-sm font-semibold ${isToday ? 'text-primary' : 'text-text'}">
                                    ${dayNum}
                                </div>
                                <div class="text-xs text-text-secondary">
                                    ${month}
                                </div>
                            </div>
                        `;
                    }).join('')}
                </div>
            `;
        }
        
        // Render habits
        let html = '';
        
        this.habits.forEach(habit => {
            if (!habit) return;
            
            const habitColor = habit.color || this.selectedColor;
            const habitTitle = habit.name || habit.title || 'Untitled Habit';
            const completedDays = habit.completed_days || [];
            
            console.log(`Habit: ${habitTitle}, Completed days: ${completedDays.join(',')}`);
            console.log(`Habit: ${habitTitle}, Color: ${habitColor}`);
            html += `
                <div class="habit-week-row" data-habit-id="${habit.habitlist_id || habit.habit_id}">
                    <div class="habit-week-info">
                        <div class="flex items-center space-x-3">
                            <div class="habit-color w-3 h-3 rounded-full flex-shrink-0" 
                                 style="background-color: ${habitColor}"></div>
                            <div class="habit-title font-medium text-text truncate" 
                                 title="${habitTitle}">${habitTitle}</div>
                        </div>
                        <div class="flex items-center">
                            <button class="edit-habit-btn opacity-70 hover:opacity-100 transition-opacity 
                                           text-text-secondary hover:text-primary p-1 ml-2" 
                                    type="button"
                                    title="Edit habit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                            <button class="delete-habit-btn opacity-70 hover:opacity-100 transition-opacity 
                                           text-text-secondary hover:text-error p-1 ml-1" 
                                    type="button"
                                    title="Delete habit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>
            `;
            
            weekDates.forEach((date, index) => {
                const day = date.getDate();
                const month = date.getMonth() + 1;
                const isCompleted = completedDays.includes(day);
                const isToday = this.isToday(date);
                const isFuture = date > new Date();
                const dateStr = this.formatDateForAPI(date);
                
                console.log(`  Day ${index}: ${day}/${month}, Completed: ${isCompleted}, Date: ${dateStr}`);
                
                let cellClasses = 'habit-day-week';
                if (isCompleted) cellClasses += ' completed';
                if (isToday) cellClasses += ' today';
                if (isFuture) cellClasses += ' future';
                if (date.getDay() === 0 || date.getDay() === 6) cellClasses += ' weekend';
                
                html += `
                    <div class="habit-cell-week">
                        <button class="${cellClasses}"
                            data-day="${day}"
                            data-date="${dateStr}"
                            data-month="${month}"
                            data-index="${index}"
                            style="${isCompleted ? `background-color: ${habitColor}` : ''}"
                            title="Day: ${day}, Month: ${month}, Date: ${dateStr}"
                            type="button"
                            ${isFuture ? 'disabled' : ''}>
                            ${isCompleted ? '✓' : day}
                        </button>
                    </div>
                `;
            });
            
            html += '</div>';
        });
        
        console.log('=== END RENDER DEBUG ===');
        
        container.innerHTML = html;
        this.attachEventListeners();
    }
    
    isToday(date) {
        const today = new Date();
        return date.getDate() === today.getDate() &&
               date.getMonth() === today.getMonth() &&
               date.getFullYear() === today.getFullYear();
    }
    
    // ==================== EVENT HANDLERS ====================
    
    attachEventListeners() {
        // Day click handlers
        document.querySelectorAll('.habit-day-week').forEach(dayBox => {
            if (!dayBox.disabled) {
                dayBox.addEventListener('click', (e) => {
                    e.stopPropagation();
                    e.preventDefault();
                    
                    console.log('=== CLICK EVENT DEBUG ===');
                    console.log('Clicked element:', dayBox);
                    
                    const clickedDay = parseInt(dayBox.dataset.day);
                    const clickedDate = dayBox.dataset.date;
                    
                    console.log('Clicked day:', clickedDay);
                    console.log('Clicked date:', clickedDate);
                    
                    const habitRow = dayBox.closest('[data-habit-id]');
                    if (habitRow) {
                        const habitId = habitRow.dataset.habitId;
                        
                        // Lấy màu từ habit-color element
                        const habitColorElement = habitRow.querySelector('.habit-color');
                        let habitColor = this.selectedColor;
                        
                        if (habitColorElement) {
                            // Lấy màu từ computed style
                            const rgbColor = window.getComputedStyle(habitColorElement).backgroundColor;
                            console.log('RGB color from element:', rgbColor);
                            
                            // Chuyển RGB sang Hex
                            habitColor = this.rgbToHex(rgbColor);
                            console.log('Converted to hex:', habitColor);
                        }
                        
                        console.log('Habit ID:', habitId);
                        console.log('Habit color (final):', habitColor);
                        console.log('=== END DEBUG ===');
                        
                        this.toggleHabitDay(habitId, clickedDay, clickedDate, habitColor, dayBox);
                    }
                });
            }
        });
        
        // Delete buttons
        document.querySelectorAll('.delete-habit-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                const habitRow = e.target.closest('[data-habit-id]');
                if (habitRow) {
                    const habitId = habitRow.dataset.habitId;
                    const habitTitle = habitRow.querySelector('.habit-title')?.textContent || 'Habit';
                    this.showDeleteModal(habitId, habitTitle);
                }
            });
        });
        // Edit buttons
        document.querySelectorAll('.edit-habit-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                const habitRow = e.target.closest('[data-habit-id]');
                if (habitRow) {
                    const habitId = habitRow.dataset.habitId;
                    const habitTitle = habitRow.querySelector('.habit-title')?.textContent || 'Habit';
                    const habitColorElement = habitRow.querySelector('.habit-color');
                    const habitColor = habitColorElement ? 
                        window.getComputedStyle(habitColorElement).backgroundColor : 
                        this.selectedColor;
                    
                    // Lấy daily target từ data attribute hoặc default
                    const dailyTarget = 1; // Bạn có thể thêm data-target attribute nếu cần
                    
                    // Convert RGB to Hex if needed
                    let finalColor = this.rgbToHex(habitColor);
                    
                    this.showEditModal(habitId, habitTitle, finalColor, dailyTarget);
                }
            });
        });
    }
    
    async toggleHabitDay(habitId, day, date, habitColor, dayBox) {
        try {
            console.log('Toggling habit:', { habitId, day, date });
            
            const response = await fetch('/habit/api/toggle', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    habit_id: habitId,
                    date: date
                })
            });
            
            if (!response.ok) throw new Error('Toggle failed');
            
            const data = await response.json();
            console.log('Toggle response:', data);
            
            if (data.success) {
                const isCompleted = data.status;
                
                // Instead of updating just the clicked box, reload all habits
                // This ensures consistency with server data
                await this.loadHabits();
                
                App.showToast(
                    isCompleted ? 'Habit completed!' : 'Habit unchecked',
                    isCompleted ? 'success' : 'info'
                );
            } else {
                App.showToast(data.error || 'Toggle failed', 'error');
            }
        } catch (error) {
            console.error('Error toggling habit:', error);
            App.showToast('Error toggling habit', 'error');
        }
    }
        
    // ==================== MODAL FUNCTIONS ====================
    
    showDeleteModal(habitId, habitTitle) {
        this.deletingHabitId = habitId;
        this.deletingHabitTitle = habitTitle;

        const modal = document.getElementById('confirm-modal');
        const message = document.getElementById('confirm-message');

        if (!modal || !message) return;
        
        message.textContent = `Are you sure you want to delete "${habitTitle}"? This action cannot be undone.`;
        
        // Hiển thị modal
        modal.style.display = 'flex';
        document.body.classList.add('modal-open');

        setTimeout(() => {
            const cancelBtn = document.getElementById('cancel-delete');
            if (cancelBtn) cancelBtn.focus();
        }, 100);
    }

    closeModal() {
        const modal = document.getElementById('confirm-modal');
        if (modal) {
            modal.style.display = 'none';
        }
        
        document.body.classList.remove('modal-open');
        this.deletingHabitId = null;
        this.deletingHabitTitle = '';
    }
    
    async deleteHabitConfirmed() {
        if (!this.deletingHabitId) return;

        try {
            const confirmBtn = document.getElementById('confirm-delete');
            if (confirmBtn) {
                confirmBtn.classList.add('loading');
                confirmBtn.disabled = true;
            }

            const response = await fetch('/habit/api/delete', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    habit_id: this.deletingHabitId
                })
            });

            if (confirmBtn) {
                confirmBtn.classList.remove('loading');
                confirmBtn.disabled = false;
            }

            if (!response.ok) throw new Error('Delete failed');

            const data = await response.json();

            if (data.success) {
                App.showToast('Habit deleted successfully', 'success');
                this.closeModal();
                this.loadHabits();
            } else {
                App.showToast(data.error || 'Failed to delete habit', 'error');
                this.closeModal();
            }
        } catch (error) {
            console.error('Error deleting habit:', error);
            App.showToast('Error deleting habit', 'error');
            this.closeModal();
        }
    }
    
    // ==================== NAVIGATION ====================
    
    prevMonth() {
        if (this.currentMonth === 1) {
            this.currentMonth = 12;
            this.currentYear--;
        } else {
            this.currentMonth--;
        }
        this.calculateWeeksInMonth();
        this.updateMonthDisplay();
        this.updateWeekDisplay();
        this.loadHabits();
    }
    
    nextMonth() {
        if (this.currentMonth === 12) {
            this.currentMonth = 1;
            this.currentYear++;
        } else {
            this.currentMonth++;
        }
        this.calculateWeeksInMonth();
        this.updateMonthDisplay();
        this.updateWeekDisplay();
        this.loadHabits();
    }
    
    prevWeek() {
        if (this.currentWeek > 0) {
            this.currentWeek--;
            this.updateWeekDisplay();
            if (this.lastData) {
                this.renderWeeklyView(this.lastData);
            }
        }
    }
    
    nextWeek() {
        if (this.currentWeek < this.weeksInMonth - 1) {
            this.currentWeek++;
            this.updateWeekDisplay();
            if (this.lastData) {
                this.renderWeeklyView(this.lastData);
            }
        }
    }
    
    goToToday() {
        const today = new Date();
        this.currentMonth = today.getMonth() + 1;
        this.currentYear = today.getFullYear();
        this.calculateWeeksInMonth();
        this.updateMonthDisplay();
        this.updateWeekDisplay();
        this.loadHabits();
    }
    
    // ==================== UTILITY FUNCTIONS ====================
    
    updateMonthDisplay() {
        const monthNames = [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ];
        
        const display = document.getElementById('current-month');
        if (display) {
            display.textContent = `${monthNames[this.currentMonth - 1]} ${this.currentYear}`;
        }
    }
    
    updateWeekDisplay() {
        const weekDisplay = document.getElementById('current-week');
        if (weekDisplay) {
            weekDisplay.textContent = `Week ${this.currentWeek + 1} of ${this.weeksInMonth}`;
        }
    }
    
    updateStats() {
        const totalHabits = this.habits.length;
        
        // Calculate weekly completions
        const weekDates = this.getWeekDates();
        const weekDays = weekDates.map(date => date.getDate());
        
        const totalWeeklyCompletions = this.habits.reduce((sum, habit) => {
            const completedDays = habit.completed_days || [];
            const weeklyCompletions = completedDays.filter(day => weekDays.includes(day)).length;
            return sum + weeklyCompletions;
        }, 0);
        
        // Update footer stats
        const totalHabitsElement = document.getElementById('total-habits');
        const totalCompletionsElement = document.getElementById('total-completions');
        
        if (totalHabitsElement) {
            totalHabitsElement.textContent = totalHabits;
        }
        
        if (totalCompletionsElement) {
            totalCompletionsElement.textContent = totalWeeklyCompletions;
        }
        
        // Update stats cards
        this.updateStatsCards(weekDays);
    }
    
    updateStatsCards(weekDays) {
        const statsContainer = document.getElementById('habit-stats');
        if (!statsContainer) return;
        
        const totalHabits = this.habits.length;
        
        // Calculate weekly completions
        const totalWeeklyCompletions = this.habits.reduce((sum, habit) => {
            const completedDays = habit.completed_days || [];
            const weeklyCompletions = completedDays.filter(day => weekDays.includes(day)).length;
            return sum + weeklyCompletions;
        }, 0);
        
        const totalPossible = totalHabits * 7; // 7 days in a week
        const completionRate = totalPossible > 0 ? 
            Math.round((totalWeeklyCompletions / totalPossible) * 100) : 0;
        
        // Calculate longest streak for current week
        const longestStreak = this.calculateCurrentStreak();
        
        statsContainer.innerHTML = `
            <div class="stat-card">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="stat-label text-text-secondary text-sm font-medium mb-1">Total Habits</div>
                        <div class="stat-value text-2xl font-bold text-text">${totalHabits}</div>
                    </div>
                    <div class="stat-icon w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="stat-label text-text-secondary text-sm font-medium mb-1">Weekly Completions</div>
                        <div class="stat-value text-2xl font-bold text-text">${totalWeeklyCompletions}</div>
                    </div>
                    <div class="stat-icon w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="stat-label text-text-secondary text-sm font-medium mb-1">Completion Rate</div>
                        <div class="stat-value text-2xl font-bold text-text">${completionRate}%</div>
                    </div>
                    <div class="stat-icon w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="stat-label text-text-secondary text-sm font-medium mb-1">Current Streak</div>
                        <div class="stat-value text-2xl font-bold text-text">${longestStreak} days</div>
                    </div>
                    <div class="stat-icon w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                    </div>
                </div>
            </div>
        `;
    }
    
    calculateCurrentStreak() {
        const today = new Date();
        const todayStr = today.toISOString().split('T')[0];
        
        let longestStreak = 0;
        
        this.habits.forEach(habit => {
            if (!habit.completed_days || habit.completed_days.length === 0) return;
            
            // Sort completed days
            const sortedDays = [...habit.completed_days].sort((a, b) => a - b);
            
            // Check for consecutive days including today
            let currentStreak = 0;
            const lastDay = sortedDays[sortedDays.length - 1];
            
            // If habit was completed today or yesterday
            if (lastDay === today.getDate() || lastDay === today.getDate() - 1) {
                currentStreak = 1;
                
                // Check backwards for consecutive days
                for (let i = sortedDays.length - 2; i >= 0; i--) {
                    if (sortedDays[i] === sortedDays[i + 1] - 1) {
                        currentStreak++;
                    } else {
                        break;
                    }
                }
            }
            
            if (currentStreak > longestStreak) {
                longestStreak = currentStreak;
            }
        });
        
        return longestStreak;
    }
    
    async addHabit() {
        const input = document.getElementById('new-habit-input');
        if (!input) return;
        
        const title = input.value.trim();
        
        if (!title) {
            App.showToast('Please enter a habit name', 'error');
            input.focus();
            return;
        }
        
        if (title.length > 50) {
            App.showToast('Habit name is too long (max 50 characters)', 'error');
            return;
        }
        
        try {
            const addBtn = document.getElementById('add-habit-btn');
            if (addBtn) {
                addBtn.classList.add('loading');
                addBtn.disabled = true;
            }
            
            // Gửi cả title và color
            const response = await fetch('/habit/api/create', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    title: title,
                    color: this.selectedColor // QUAN TRỌNG: Gửi màu đã chọn
                })
            });
            
            if (addBtn) {
                addBtn.classList.remove('loading');
                addBtn.disabled = false;
            }
            
            if (!response.ok) throw new Error('Create failed');
            
            const data = await response.json();
            
            if (data.success) {
                App.showToast('Habit added successfully!', 'success');
                input.value = '';
                input.focus();
                await this.loadHabits();
                this.updateStats();
            } else {
                App.showToast(data.error || 'Failed to add habit', 'error');
            }
        } catch (error) {
            console.error('Error adding habit:', error);
            App.showToast('Error adding habit', 'error');
            const addBtn = document.getElementById('add-habit-btn');
            if (addBtn) {
                addBtn.classList.remove('loading');
                addBtn.disabled = false;
            }
        }
    }
    
    getEmptyStateHTML() {
        return `
            <div class="text-center py-12">
                <div class="w-16 h-16 mx-auto mb-4 text-text-secondary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-text mb-2">No habits yet</h3>
                <p class="text-text-secondary">Start by adding your first habit above</p>
            </div>
        `;
    }

    formatDateForAPI(date) {
        // Đảm bảo date string đúng định dạng YYYY-MM-DD
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    rgbToHex(color) {
        // Nếu đã là hex, trả về luôn
        if (color.startsWith('#')) {
            return color;
        }
        
        // Xử lý rgb(r, g, b) hoặc rgba(r, g, b, a)
        const match = color.match(/^rgba?\((\d+),\s*(\d+),\s*(\d+)(?:,\s*[\d.]+)?\)$/);
        
        if (!match) {
            console.warn('Invalid color format:', color);
            return this.selectedColor; // Trả về màu mặc định
        }
        
        // Chuyển từ RGB sang Hex
        const r = parseInt(match[1]);
        const g = parseInt(match[2]);
        const b = parseInt(match[3]);
        
        // Đảm bảo giá trị trong khoảng 0-255
        const clamp = (value) => Math.min(255, Math.max(0, value));
        
        const hexR = clamp(r).toString(16).padStart(2, '0');
        const hexG = clamp(g).toString(16).padStart(2, '0');
        const hexB = clamp(b).toString(16).padStart(2, '0');
        
        return `#${hexR}${hexG}${hexB}`.toUpperCase();
    }
}

// Initialize Habit Tracker when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('habits-container')) {
        window.habitTracker = new ModernHabitTracker();
        
        setTimeout(() => {
            const input = document.getElementById('new-habit-input');
            if (input) input.focus();
        }, 300);
    }
});