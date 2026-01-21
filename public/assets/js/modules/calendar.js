// Calendar Module JavaScript
class CalendarApp {
    constructor() {
        this.currentMonth = CalendarConfig.currentMonth;
        this.currentYear = CalendarConfig.currentYear;
        this.selectedDate = CalendarConfig.selectedDate;
        this.userId = CalendarConfig.userId;
        this.events = [];
        
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.loadMonthGrid();
        this.loadEvents();
    }
    
    bindEvents() {
        // Navigation
        document.getElementById('prevMonth')?.addEventListener('click', () => this.navigateMonth(-1));
        document.getElementById('nextMonth')?.addEventListener('click', () => this.navigateMonth(1));
        document.getElementById('todayBtn')?.addEventListener('click', () => this.goToToday());
        
        // Header day click
        document.querySelectorAll('.calendar-day-header').forEach(header => {
            header.addEventListener('click', () => {
                const date = header.dataset.date;
                this.selectDate(date);
            });
        });
        
        // Create event button
        document.getElementById('createEventBtn')?.addEventListener('click', () => this.showEventModal());
        
        // Event form
        document.getElementById('eventForm')?.addEventListener('submit', (e) => this.saveEvent(e));
        
        // Modal close
        document.querySelectorAll('.modal-close').forEach(btn => {
            btn.addEventListener('click', () => this.hideModal());
        });
        
        // All-day checkbox
        document.getElementById('isAllDay')?.addEventListener('change', (e) => {
            this.toggleAllDay(e.target.checked);
        });
        
        // Close modal when clicking outside
        document.getElementById('eventModal')?.addEventListener('click', (e) => {
            if (e.target.id === 'eventModal') {
                this.hideModal();
            }
        });
    }
    
    // Load month grid
    loadMonthGrid() {
        const monthGrid = document.getElementById('monthGrid');
        if (!monthGrid) return;
        
        monthGrid.innerHTML = '';
        
        const firstDay = new Date(this.currentYear, this.currentMonth - 1, 1);
        const lastDay = new Date(this.currentYear, this.currentMonth, 0);
        const daysInMonth = lastDay.getDate();
        const startingDay = firstDay.getDay(); // 0 = Sunday
        
        // Update header
        document.getElementById('currentMonthYear').textContent = 
            `${firstDay.toLocaleString('default', { month: 'long' })} ${this.currentYear}`;
        
        // Create empty cells for previous month
        const prevMonthLastDay = new Date(this.currentYear, this.currentMonth - 1, 0).getDate();
        for (let i = 0; i < startingDay; i++) {
            const day = prevMonthLastDay - startingDay + i + 1;
            const date = new Date(this.currentYear, this.currentMonth - 2, day);
            monthGrid.appendChild(this.createDayCell(date, true));
        }
        
        // Create cells for current month
        const today = new Date();
        const selectedDate = new Date(this.selectedDate);
        
        for (let day = 1; day <= daysInMonth; day++) {
            const date = new Date(this.currentYear, this.currentMonth - 1, day);
            const isToday = date.toDateString() === today.toDateString();
            const isSelected = date.toDateString() === selectedDate.toDateString();
            
            monthGrid.appendChild(this.createDayCell(date, false, isToday, isSelected));
        }
        
        // Create empty cells for next month
        const totalCells = 42; // 6 weeks
        const remainingCells = totalCells - (startingDay + daysInMonth);
        for (let i = 1; i <= remainingCells; i++) {
            const date = new Date(this.currentYear, this.currentMonth, i);
            monthGrid.appendChild(this.createDayCell(date, true));
        }
        
        // Update header selection
        this.updateHeaderSelection();
    }
    
    // Create day cell
    createDayCell(date, isOtherMonth, isToday = false, isSelected = false) {
        const day = date.getDate();
        const month = date.getMonth() + 1;
        const year = date.getFullYear();
        const dateString = `${year}-${month.toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`;
        
        const dayCell = document.createElement('div');
        dayCell.className = `calendar-day ${isOtherMonth ? 'other-month' : ''} ${isToday ? 'today' : ''} ${isSelected ? 'selected' : ''}`;
        dayCell.dataset.date = dateString;
        
        // Date number
        const dateNumber = document.createElement('div');
        dateNumber.className = 'date-number';
        dateNumber.textContent = day;
        dayCell.appendChild(dateNumber);
        
        // Events container
        const eventsContainer = document.createElement('div');
        eventsContainer.className = 'day-events';
        dayCell.appendChild(eventsContainer);
        
        // Click event
        dayCell.addEventListener('click', () => this.selectDate(dateString));
        
        return dayCell;
    }
    
    // Select date
    selectDate(dateString) {
        this.selectedDate = dateString;
        
        // Update month grid selection
        document.querySelectorAll('.calendar-day').forEach(cell => {
            cell.classList.remove('selected');
            if (cell.dataset.date === dateString) {
                cell.classList.add('selected');
            }
        });
        
        // Update header selection
        this.updateHeaderSelection();
        
        // Update events list
        this.updateDayEvents();
        
        // Update title
        const date = new Date(dateString);
        document.getElementById('dayEventsTitle').textContent = 
            `Events for ${date.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' })}`;
    }
    
    // Update header selection
    updateHeaderSelection() {
        document.querySelectorAll('.calendar-day-header').forEach(header => {
            header.classList.remove('selected');
            if (header.dataset.date === this.selectedDate) {
                header.classList.add('selected');
            }
        });
    }
    
    // Load events from API
    async loadEvents() {
        try {
            this.showLoading();
            
            const startDate = `${this.currentYear}-${this.currentMonth.toString().padStart(2, '0')}-01`;
            const endDate = new Date(this.currentYear, this.currentMonth, 0)
                .toISOString().split('T')[0];
            
            // Debug: Log URL
            const url = `/calendar/api?action=getEvents&start=${startDate}&end=${endDate}`;
            console.log('🔍 Calling API:', url);
            
            const response = await fetch(url);
            
            // Debug: Log response status
            console.log('📊 Response status:', response.status, response.statusText);
            
            const text = await response.text();
            console.log('📄 Raw response:', text);
            
            let data;
            try {
                data = JSON.parse(text);
                console.log('✅ Parsed JSON:', data);
            } catch (e) {
                console.error('❌ JSON parse error:', e);
                console.log('📝 Raw text:', text);
                throw new Error('Invalid JSON response from server');
            }
            
            if (data.success) {
                this.events = data.events || [];
                console.log('📅 Loaded events:', this.events.length);
                this.renderEvents();
                this.updateDayEvents();
            } else {
                throw new Error(data.message || 'Failed to load events');
            }
        } catch (error) {
            console.error('❌ Error loading events:', error);
            App.showToast('Failed to load events: ' + error.message, 'error');
            
            // Fallback
            this.events = [];
            this.renderEvents();
            this.updateDayEvents();
        } finally {
            this.hideLoading();
        }
    }
        
    // Render events in month grid
    renderEvents() {
        console.log('🎨 Rendering events (multi-day support):', this.events);
        
        document.querySelectorAll('.calendar-day').forEach(cell => {
            const cellDate = cell.dataset.date;
            const eventsContainer = cell.querySelector('.day-events');
            if (!eventsContainer) return;
            
            eventsContainer.innerHTML = '';
            
            const dayEvents = this.events.filter(event => {
                if (!event.start_at) return false;
                
                const eventStartDate = event.start_at.split(' ')[0]; // YYYY-MM-DD
                const eventEndDate = event.end_at ? event.end_at.split(' ')[0] : eventStartDate;
                
                // Kiểm tra nếu cellDate nằm trong khoảng event
                const cellDateTime = new Date(cellDate).getTime();
                const startDateTime = new Date(eventStartDate).getTime();
                const endDateTime = new Date(eventEndDate).getTime();
                
                // Event kéo dài nhiều ngày
                return cellDateTime >= startDateTime && cellDateTime <= endDateTime;
            });
            
            console.log(`📆 Date ${cellDate} has ${dayEvents.length} events (multi-day support)`);
            
            // Add has-events class
            if (dayEvents.length > 0) {
                cell.classList.add('has-events');
            } else {
                cell.classList.remove('has-events');
            }
            
            // Sort events by start time
            dayEvents.sort((a, b) => {
                const timeA = a.start_at ? a.start_at.split(' ')[1] || '00:00:00' : '00:00:00';
                const timeB = b.start_at ? b.start_at.split(' ')[1] || '00:00:00' : '00:00:00';
                return timeA.localeCompare(timeB);
            });
            
            // Show first 3 events
            const showCount = 3;
            const eventsToShow = dayEvents.slice(0, showCount);
            const hasMore = dayEvents.length > showCount;
            
            eventsToShow.forEach(event => {
                const eventEl = this.createEventElement(event, cellDate);
                eventsContainer.appendChild(eventEl);
            });
            
            // Show "more" indicator
            if (hasMore) {
                const moreEl = document.createElement('div');
                moreEl.className = 'more-events';
                moreEl.textContent = `+${dayEvents.length - showCount} more`;
                moreEl.addEventListener('click', (e) => {
                    e.stopPropagation();
                    this.selectDate(cellDate);
                });
                eventsContainer.appendChild(moreEl);
            }
        });
    }

    // Create event element for grid với multi-day support
    createEventElement(event, cellDate = null) {
        console.log('✨ Creating event element (multi-day):', event);
        
        const eventEl = document.createElement('div');
        eventEl.className = `day-event event-type-${event.event_type || 'event'}`;
        eventEl.dataset.eventId = event.event_id;
        
        // Thêm class cho các loại events
        if (event.is_all_day == 1) {
            eventEl.classList.add('is-all-day');
        }
        
        if (this.isMultiDayEvent(event)) {
            eventEl.classList.add('multi-day');
            
            // Kiểm tra vị trí của event
            const position = this.getEventPositionInCell(event, cellDate);
            eventEl.classList.add(`multi-day-${position}`);
            
            // Nếu là all-day event kéo dài nhiều ngày
            if (event.is_all_day == 1) {
                eventEl.classList.add('full-cell');
            }
        }
        
        // Thêm indicator cho event time
        const timeIndicator = document.createElement('span');
        timeIndicator.className = 'event-time-indicator';
        timeIndicator.textContent = this.getEventTimeIndicator(event, cellDate);
        
        eventEl.title = `${event.title} - ${this.formatEventTime(event)}`;
        
        // Priority dot
        const priorityDot = document.createElement('span');
        priorityDot.className = `event-priority-dot priority-${event.priority || 'medium'}`;
        eventEl.appendChild(priorityDot);
        
        // Event title (truncated)
        const title = document.createElement('span');
        const eventTitle = event.title || 'Untitled';
        title.textContent = eventTitle.length > 15 ? eventTitle.substring(0, 15) + '...' : eventTitle;
        eventEl.appendChild(title);
        
        // Thêm time indicator
        if (timeIndicator.textContent) {
            eventEl.appendChild(timeIndicator);
        }
        
        // Click event
        eventEl.addEventListener('click', (e) => {
            e.stopPropagation();
            console.log('🖱️ Event clicked:', event);
            this.showEventModal(event);
        });
        
        return eventEl;
    }

    // Get time indicator for event in specific cell
    getEventTimeIndicator(event, cellDate) {
        if (!event.start_at || !cellDate) return '';
        
        const eventStartDate = event.start_at.split(' ')[0];
        const eventEndDate = event.end_at ? event.end_at.split(' ')[0] : eventStartDate;
        
        // Nếu là single day hoặc all-day, không cần indicator
        if (event.is_all_day == 1 || eventStartDate === eventEndDate) return '';
        
        // Chỉ hiển thị thời gian ở ngày đầu tiên
        if (cellDate === eventStartDate) {
            const startTime = event.start_at.split(' ')[1] || '00:00:00';
            const [hours, minutes] = startTime.split(':');
            const hour = parseInt(hours);
            const ampm = hour >= 12 ? 'pm' : 'am';
            const hour12 = hour % 12 || 12;
            return ` ${hour12}:${minutes}${ampm}`;
        }
        
        return '';
    }
// Kiểm tra event có kéo dài nhiều ngày không
isMultiDayEvent(event) {
    if (!event.start_at || !event.end_at) return false;
    
    const startDate = event.start_at.split(' ')[0];
    const endDate = event.end_at.split(' ')[0];
    
    return startDate !== endDate;
}

// Xác định vị trí của event trong cell (đầu, giữa, cuối)
getEventPositionInCell(event, cellDate) {
    if (!this.isMultiDayEvent(event) || !cellDate) return 'single';
    
    const startDate = event.start_at.split(' ')[0];
    const endDate = event.end_at.split(' ')[0];
    
    if (cellDate === startDate) return 'start';
    if (cellDate === endDate) return 'end';
    return 'middle';
}
    
    // Update day events list
    updateDayEvents() {
        const container = document.getElementById('dayEventsList');
        if (!container) {
            console.error('❌ dayEventsList container not found');
            return;
        }
        
        console.log('📅 Updating day events for:', this.selectedDate);
        console.log('📋 All events:', this.events);
        
        const dayEvents = this.events.filter(event => {
            if (!event.start_at) {
                console.warn('⚠️ Event missing start_at:', event);
                return false;
            }
            
            const eventStartDate = event.start_at.split(' ')[0];
            const eventEndDate = event.end_at ? event.end_at.split(' ')[0] : eventStartDate;
            
            // Kiểm tra nếu selectedDate nằm trong khoảng event
            const selectedDateTime = new Date(this.selectedDate).getTime();
            const startDateTime = new Date(eventStartDate).getTime();
            const endDateTime = new Date(eventEndDate).getTime();
            
            const isInRange = selectedDateTime >= startDateTime && selectedDateTime <= endDateTime;
            
            console.log(`🔍 Checking event: ${event.title} | ${eventStartDate} - ${eventEndDate} | Selected: ${this.selectedDate} | In range: ${isInRange}`);
            
            return isInRange;
        });
        
        console.log(`📊 Found ${dayEvents.length} events for ${this.selectedDate}:`, dayEvents);
        
        if (dayEvents.length === 0) {
            container.innerHTML = `
                <div class="text-center py-8 text-text-secondary">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" class="mx-auto mb-4 opacity-50">
                        <path d="M19 4H5C3.89543 4 3 4.89543 3 6V20C3 21.1046 3.89543 22 5 22H19C20.1046 22 21 21.1046 21 20V6C21 4.89543 20.1046 4 19 4Z" 
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M16 2V6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M8 2V6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M3 10H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <p>No events for this day</p>
                </div>
            `;
            return;
        }
        
        // SORT đúng: Theo thời gian bắt đầu
        dayEvents.sort((a, b) => {
            const timeA = a.start_at ? a.start_at.split(' ')[1] || '00:00:00' : '00:00:00';
            const timeB = b.start_at ? b.start_at.split(' ')[1] || '00:00:00' : '00:00:00';
            console.log(`Sorting: ${timeA} vs ${timeB}`);
            return timeA.localeCompare(timeB);
        });
        
        console.log('✅ Sorted events:', dayEvents);
        
        container.innerHTML = dayEvents.map(event => {
            console.log('🎨 Rendering event in list:', event);
            return `
                <div class="event-item ${event.status === 'completed' ? 'completed' : ''}" 
                    data-event-id="${event.event_id}">
                    <div class="flex justify-between items-start mb-2">
                        <div class="flex items-center">
                            <span class="event-priority-dot priority-${event.priority || 'medium'} mr-3"></span>
                            <h4 class="font-semibold text-text">${this.escapeHtml(event.title || 'Untitled')}</h4>
                            <span class="badge badge-${event.event_type || 'event'} ml-3">
                                ${(event.event_type || 'event').charAt(0).toUpperCase() + (event.event_type || 'event').slice(1)}
                            </span>
                        </div>
                        <div class="flex space-x-2">
                            ${(!event.status || event.status === 'scheduled') ? `
                                <button class="complete-btn p-1 text-green-600 hover:text-green-800" 
                                        data-event-id="${event.event_id}">
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                        <path d="M13.3333 4L6 11.3333L2.66667 8" 
                                            stroke="currentColor" stroke-width="2" 
                                            stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </button>
                            ` : ''}
                            <button class="edit-btn p-1 text-blue-600 hover:text-blue-800" 
                                    data-event-id="${event.event_id}">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                    <path d="M11.3333 2.00001C11.5084 1.82491 11.7163 1.686 11.9452 1.59081C12.1741 1.49562 12.4198 1.44592 12.668 1.44446C12.9162 1.443 13.1624 1.48981 13.3924 1.58224C13.6224 1.67467 13.8316 1.81081 14.008 1.9829C14.1844 2.15499 14.3246 2.35965 14.4205 2.58505C14.5164 2.81044 14.566 3.05202 14.5663 3.29591C14.5666 3.5398 14.5176 3.7815 14.4222 4.00713C14.3268 4.23277 14.187 4.43777 14.011 4.61001L5.21867 13.4023L1.33333 14.6667L2.59767 10.7813L11.3333 2.00001Z" 
                                        stroke="currentColor" stroke-width="2" 
                                        stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                            <button class="delete-btn p-1 text-red-600 hover:text-red-800" 
                                    data-event-id="${event.event_id}">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                    <path d="M2 4H3.33333H14" 
                                        stroke="currentColor" stroke-width="2" 
                                        stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M5.33333 4V2.66667C5.33333 2.31305 5.47381 1.97391 5.72386 1.72386C5.97391 1.47381 6.31305 1.33334 6.66667 1.33334H9.33333C9.68696 1.33334 10.0261 1.47381 10.2761 1.72386C10.5262 1.97391 10.6667 2.31305 10.6667 2.66667V4M12.6667 4V13.3333C12.6667 13.687 12.5262 14.0261 12.2761 14.2761C12.0261 14.5262 11.687 14.6667 11.3333 14.6667H4.66667C4.31305 14.6667 3.97391 14.5262 3.72386 14.2761C3.47381 14.0261 3.33333 13.687 3.33333 13.3333V4H12.6667Z" 
                                        stroke="currentColor" stroke-width="2" 
                                        stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="event-time">
                        ${this.formatEventTime(event)}
                    </div>
                    ${event.description ? `
                        <p class="text-text-secondary text-sm mt-2">${this.escapeHtml(event.description)}</p>
                    ` : ''}
                    ${event.location ? `
                        <div class="flex items-center mt-2 text-sm text-text-secondary">
                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" class="mr-1">
                                <path d="M6 6.66667C7.10457 6.66667 8 5.77124 8 4.66667C8 3.5621 7.10457 2.66667 6 2.66667C4.89543 2.66667 4 3.5621 4 4.66667C4 5.77124 4.89543 6.66667 6 6.66667Z" 
                                    stroke="currentColor" stroke-width="1.5"/>
                                <path d="M6 11.3333C8.66667 8.66667 10 6.7381 10 4.66667C10 2.45753 8.20938 0.666672 6 0.666672C3.79086 0.666672 2 2.45753 2 4.66667C2 6.7381 3.33333 8.66667 6 11.3333Z" 
                                    stroke="currentColor" stroke-width="1.5"/>
                            </svg>
                            ${this.escapeHtml(event.location)}
                        </div>
                    ` : ''}
                </div>
            `;
        }).join('');
        
        console.log('✅ Day events HTML updated');
        
        // Bind event buttons
        container.querySelectorAll('.complete-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                this.completeEvent(btn.dataset.eventId);
            });
        });
        
        container.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                const event = this.events.find(e => e.event_id === btn.dataset.eventId);
                if (event) this.showEventModal(event);
            });
        });
        
        container.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                this.deleteEvent(btn.dataset.eventId);
            });
        });
    }
    
    // Format event time for display
    formatEventTime(event) {
        console.log('🕒 Formatting time (multi-day):', event);
        
        if (!event.start_at) {
            console.log('⚠️ No start_at for event:', event);
            return 'Time not set';
        }
        
        const startDate = event.start_at.split(' ')[0];
        const startTime = event.start_at.split(' ')[1] || '00:00:00';
        const endDate = event.end_at ? event.end_at.split(' ')[0] : startDate;
        const endTime = event.end_at ? event.end_at.split(' ')[1] : null;
        
        // All-day event
        if (event.is_all_day == 1) {
            if (startDate === endDate) {
                return 'All day';
            } else {
                // Multi-day all-day event
                const startFormatted = new Date(startDate).toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                const endFormatted = new Date(endDate).toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                return `All day · ${startFormatted} - ${endFormatted}`;
            }
        }
        
        // Format time function
        const formatTime = (timeStr) => {
            const [hours, minutes] = timeStr.split(':');
            const hour = parseInt(hours);
            const ampm = hour >= 12 ? 'pm' : 'am';
            const hour12 = hour % 12 || 12;
            return `${hour12}:${minutes} ${ampm}`;
        };
        
        // Single day event with time
        if (startDate === endDate) {
            if (endTime) {
                return `${formatTime(startTime)} - ${formatTime(endTime)}`;
            }
            return formatTime(startTime);
        }
        
        // Multi-day event with time
        const startFormatted = new Date(startDate).toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
        const endFormatted = new Date(endDate).toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
        
        if (endTime) {
            return `${startFormatted}, ${formatTime(startTime)} - ${endFormatted}, ${formatTime(endTime)}`;
        }
        
        return `${startFormatted}, ${formatTime(startTime)} - ${endFormatted}`;
    }
    
    // Navigation
    navigateMonth(direction) {
        let newMonth = this.currentMonth + direction;
        let newYear = this.currentYear;
        
        if (newMonth > 12) {
            newMonth = 1;
            newYear++;
        } else if (newMonth < 1) {
            newMonth = 12;
            newYear--;
        }
        
        this.currentMonth = newMonth;
        this.currentYear = newYear;
        
        // Update URL
        const url = new URL(window.location);
        url.searchParams.set('month', newMonth);
        url.searchParams.set('year', newYear);
        window.history.pushState({}, '', url);
        
        this.loadMonthGrid();
        this.loadEvents();
    }
    
    goToToday() {
        const today = new Date();
        this.currentMonth = today.getMonth() + 1;
        this.currentYear = today.getFullYear();
        this.selectedDate = today.toISOString().split('T')[0];
        
        // Update URL
        const url = new URL(window.location);
        url.searchParams.set('month', this.currentMonth);
        url.searchParams.set('year', this.currentYear);
        url.searchParams.set('date', this.selectedDate);
        window.history.pushState({}, '', url);
        
        this.loadMonthGrid();
        this.loadEvents();
        this.selectDate(this.selectedDate);
    }
    
    // Event modal
    showEventModal(event = null) {
        const modal = document.getElementById('eventModal');
        const form = document.getElementById('eventForm');
        
        if (event) {
            // Edit mode
            document.querySelector('.modal-title').textContent = 'Edit Event';
            form.dataset.mode = 'edit';
            
            // Fill form
            document.getElementById('eventId').value = event.event_id || '';
            document.getElementById('eventCalendarId').value = event.calendar_id || '';
            document.getElementById('eventTitle').value = event.title || '';
            document.getElementById('eventType').value = event.event_type || 'event';
            document.getElementById('eventDescription').value = event.description || '';
            document.getElementById('eventLocation').value = event.location || '';
            document.getElementById('eventPriority').value = event.priority || 'medium';
            document.getElementById('isAllDay').checked = event.is_all_day == 1;
            
            // Format dates
            if (event.start_at) {
                const startDate = new Date(event.start_at);
                document.getElementById('eventStartDate').value = startDate.toISOString().split('T')[0];
                document.getElementById('eventStartTime').value = this.formatTimeForInput(startDate);
            } else {
                document.getElementById('eventStartDate').value = this.selectedDate;
                document.getElementById('eventStartTime').value = '09:00';
            }
            
            if (event.end_at) {
                const endDate = new Date(event.end_at);
                document.getElementById('eventEndDate').value = endDate.toISOString().split('T')[0];
                document.getElementById('eventEndTime').value = this.formatTimeForInput(endDate);
            } else {
                document.getElementById('eventEndDate').value = '';
                document.getElementById('eventEndTime').value = '';
            }
        } else {
            // Create mode - default to selected date
            document.querySelector('.modal-title').textContent = 'Create Event';
            form.dataset.mode = 'create';
            
            // Reset form
            form.reset();
            document.getElementById('eventId').value = '';
            
            // Set default values to selected date
            const selectedDate = new Date(this.selectedDate);
            document.getElementById('eventStartDate').value = this.selectedDate;
            document.getElementById('eventStartTime').value = '09:00';
            
            // End date same as start, time +1 hour
            document.getElementById('eventEndDate').value = this.selectedDate;
            document.getElementById('eventEndTime').value = '10:00';
            
            // Set default values
            document.getElementById('eventType').value = 'event';
            document.getElementById('eventPriority').value = 'medium';
        }
        
        // Show modal
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    
    hideModal() {
        const modal = document.getElementById('eventModal');
        modal.style.display = 'none';
        document.body.style.overflow = '';
        
        const form = document.getElementById('eventForm');
        form.reset();
        form.dataset.mode = '';
    }
    
    // Save event
    async saveEvent(e) {
        e.preventDefault();
        
        const form = document.getElementById('eventForm');
        const isEdit = form.dataset.mode === 'edit';
        
        // Get form values
        const startDate = document.getElementById('eventStartDate').value;
        const startTime = document.getElementById('eventStartTime').value;
        const endDate = document.getElementById('eventEndDate').value;
        const endTime = document.getElementById('eventEndTime').value;
        const isAllDay = document.getElementById('isAllDay').checked;
        
        // Validation
        if (!startDate) {
            App.showToast('Start date is required', 'error');
            document.getElementById('eventStartDate').focus();
            return;
        }
        
        const title = document.getElementById('eventTitle').value.trim();
        if (!title) {
            App.showToast('Event title is required', 'error');
            document.getElementById('eventTitle').focus();
            return;
        }
        
        // Chuẩn bị dữ liệu
        const params = new URLSearchParams();
        params.append('action', isEdit ? 'updateEvent' : 'createEvent');
        
        // Thêm event_id nếu là edit
        if (isEdit) {
            const eventId = document.getElementById('eventId').value;
            if (eventId) {
                params.append('id', eventId);
            }
        }
        
        // Thêm các trường
        params.append('title', title);
        params.append('description', document.getElementById('eventDescription').value.trim());
        params.append('location', document.getElementById('eventLocation').value.trim());
        params.append('event_type', document.getElementById('eventType').value);
        params.append('priority', document.getElementById('eventPriority').value);
        params.append('is_all_day', isAllDay ? '1' : '0');
        
        // Xử lý thời gian
        const startDateTime = isAllDay ? `${startDate}T00:00:00` : `${startDate}T${startTime}:00`;
        params.append('start_at', startDateTime);
        
        if (endDate && endTime) {
            const endDateTime = isAllDay ? `${endDate}T23:59:59` : `${endDate}T${endTime}:00`;
            params.append('end_at', endDateTime);
        }
        
        try {
            this.showLoading();
            
            // Luôn dùng POST
            const response = await fetch('/calendar/api', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: params.toString()
            });
            
            const data = await response.json();
            
            if (data.success) {
                App.showToast(
                    data.message || (isEdit ? 'Event updated successfully' : 'Event created successfully'),
                    'success'
                );
                
                this.hideModal();
                this.loadEvents();
                this.updateDayEvents();
            } else {
                throw new Error(data.message || 'Failed to save event');
            }
        } catch (error) {
            console.error('Error saving event:', error);
            App.showToast(error.message, 'error');
        } finally {
            this.hideLoading();
        }
    }
    
    // Complete event
    async completeEvent(eventId) {
        if (!confirm('Mark this event as completed?')) return;
        
        try {
            this.showLoading();
            
            const params = new URLSearchParams();
            params.append('action', 'updateEventStatus');
            params.append('id', eventId);
            params.append('status', 'completed');
            
            const response = await fetch('/calendar/api', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: params.toString()
            });
            
            const data = await response.json();
            
            if (data.success) {
                App.showToast('Event marked as completed', 'success');
                this.loadEvents();
                this.updateDayEvents();
            } else {
                throw new Error(data.message || 'Failed to complete event');
            }
        } catch (error) {
            console.error('Error completing event:', error);
            App.showToast(error.message, 'error');
        } finally {
            this.hideLoading();
        }
    }

    // Delete event (dùng POST)
    async deleteEvent(eventId) {
        if (!confirm('Are you sure you want to delete this event?')) return;
        
        try {
            this.showLoading();
            
            const params = new URLSearchParams();
            params.append('action', 'deleteEvent');
            params.append('id', eventId);
            
            const response = await fetch('/calendar/api', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: params.toString()
            });
            
            const data = await response.json();
            
            if (data.success) {
                App.showToast('Event deleted successfully', 'success');
                this.loadEvents();
                this.updateDayEvents();
            } else {
                throw new Error(data.message || 'Failed to delete event');
            }
        } catch (error) {
            console.error('Error deleting event:', error);
            App.showToast(error.message, 'error');
        } finally {
            this.hideLoading();
        }
    }
    
    // Toggle all-day event
    toggleAllDay(isAllDay) {
        const startTimeInput = document.getElementById('eventStartTime');
        const endTimeInput = document.getElementById('eventEndTime');
        
        if (isAllDay) {
            startTimeInput.disabled = true;
            endTimeInput.disabled = true;
            startTimeInput.value = '';
            endTimeInput.value = '';
        } else {
            startTimeInput.disabled = false;
            endTimeInput.disabled = false;
            if (!startTimeInput.value) startTimeInput.value = '09:00';
            if (!endTimeInput.value) endTimeInput.value = '10:00';
        }
    }
    
    // Helper methods
    formatTimeForInput(date) {
        const hours = date.getHours().toString().padStart(2, '0');
        const minutes = date.getMinutes().toString().padStart(2, '0');
        return `${hours}:${minutes}`;
    }
    
    escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    showLoading() {
        const overlay = document.getElementById('loadingOverlay');
        if (overlay) overlay.style.display = 'flex';
    }
    
    hideLoading() {
        const overlay = document.getElementById('loadingOverlay');
        if (overlay) overlay.style.display = 'none';
    }
}

// Initialize calendar app when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.calendarApp = new CalendarApp();
    
    // Handle browser back/forward buttons
    window.addEventListener('popstate', function() {
        // Reload the calendar with URL parameters
        const urlParams = new URLSearchParams(window.location.search);
        const month = urlParams.get('month') || CalendarConfig.currentMonth;
        const year = urlParams.get('year') || CalendarConfig.currentYear;
        const date = urlParams.get('date') || CalendarConfig.selectedDate;
        
        window.calendarApp.currentMonth = parseInt(month);
        window.calendarApp.currentYear = parseInt(year);
        window.calendarApp.selectedDate = date;
        
        window.calendarApp.loadMonthGrid();
        window.calendarApp.loadEvents();
        window.calendarApp.selectDate(date);
    });
});