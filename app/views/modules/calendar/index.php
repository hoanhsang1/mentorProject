<!-- ==================== -->
<!--     CALENDAR PAGE    -->
<!-- ==================== -->

<div class="calendar-container">
    <!-- Calendar Header -->
    <div class="calendar-header card">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gradient mb-2">📅 Calendar</h1>
                <div class="text-text-secondary">
                    Manage your schedule and events
                </div>
            </div>
            
            <!-- Calendar Controls -->
            <div class="flex items-center space-x-3">
                <!-- Today Button -->
                <button id="todayBtn" class="btn btn-secondary">
                    Today
                </button>
                
                <!-- Month Navigation -->
                <div class="flex items-center space-x-1 bg-white border border-border rounded-lg px-3 py-1">
                    <button id="prevMonth" class="btn btn-icon p-1 hover:bg-bg-input rounded">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                            <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                    
                    <h2 id="currentMonthYear" class="text-lg font-semibold min-w-32 text-center text-text">
                        <?= date('F Y', mktime(0, 0, 0, $currentMonth, 1, $currentYear)) ?>
                    </h2>
                    
                    <button id="nextMonth" class="btn btn-icon p-1 hover:bg-bg-input rounded">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                            <path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </div>
                
                <!-- Create Event Button -->
                <button id="createEventBtn" class="btn btn-primary">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" class="mr-2">
                        <path d="M8 3V13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        <path d="M3 8H13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    New Event
                </button>
            </div>
        </div>
    </div>

    <!-- Main Calendar Grid -->
    <div class="card">
        <!-- Calendar Grid Header (Days of Week) -->
        <div class="calendar-grid-header">
            <div class="grid">
                <?php
                // Tạo header với đúng thứ tự (CN = 0, T2 = 1, ...)
                $daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                $currentDate = new DateTime($selectedDate);
                
                // Tính ngày đầu tuần (Chủ nhật)
                $startOfWeek = clone $currentDate;
                $startOfWeek->modify('last sunday');
                
                for ($i = 0; $i < 7; $i++) {
                    $dayDate = clone $startOfWeek;
                    $dayDate->modify("+{$i} days");
                    
                    $isToday = $dayDate->format('Y-m-d') === date('Y-m-d');
                    $isSelected = $dayDate->format('Y-m-d') === $selectedDate;
                    
                    $dayClass = 'calendar-day-header';
                    if ($isToday) $dayClass .= ' today';
                    if ($isSelected) $dayClass .= ' selected';
                    ?>
                    <div class="<?= $dayClass ?>" data-date="<?= $dayDate->format('Y-m-d') ?>">
                        <div class="day-name"><?= $daysOfWeek[$i] ?></div>
                        <!-- <div class="day-number"><?= $dayDate->format('j') ?></div> -->
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
        
        <!-- Month Grid -->
        <div id="monthGrid" class="calendar-month-grid">
            <!-- Dynamic content sẽ được render bằng JS -->
        </div>
    </div>
    
    <!-- Events for Selected Day -->
    <div class="card mt-4">
        <div class="card-header">
            <h3 class="card-title" id="dayEventsTitle">
                Events for <?= date('F j, Y', strtotime($selectedDate)) ?>
            </h3>
        </div>
        <div class="p-6">
            <div id="dayEventsList" class="space-y-3">
                <!-- Events sẽ được load bằng JS -->
                <div class="empty-state">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" class="mx-auto mb-4 opacity-50">
                        <path d="M19 4H5C3.89543 4 3 4.89543 3 6V20C3 21.1046 3.89543 22 5 22H19C20.1046 22 21 21.1046 21 20V6C21 4.89543 20.1046 4 19 4Z" 
                              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M16 2V6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M8 2V6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M3 10H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <p>No events for this day</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Event Modal (Create/Edit) -->
<div id="eventModal" class="modal">
    <div class="modal-overlay" onclick="hideModal()"></div>
    <div class="modal-card">
        <div class="modal-header">
            <h3 class="modal-title">Create Event</h3>
            <button class="modal-close" onclick="hideModal()">✕</button>
        </div>
        <form id="eventForm" class="modal-body">
            <input type="hidden" id="eventId">
            <input type="hidden" id="eventCalendarId">
            
            <div class="form-group mb-4">
                <label class="form-label" for="eventTitle">Title *</label>
                <input type="text" id="eventTitle" class="modal-input" placeholder="Enter event title" required>
            </div>
            
            <div class="form-group mb-4">
                <label class="form-label" for="eventDescription">Description</label>
                <textarea id="eventDescription" class="modal-input" rows="3" placeholder="Enter description (optional)"></textarea>
            </div>
            
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div class="form-group">
                    <label class="form-label" for="eventStartDate">Start Date *</label>
                    <input type="date" id="eventStartDate" class="modal-input" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="eventStartTime">Start Time</label>
                    <input type="time" id="eventStartTime" class="modal-input" value="09:00">
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="form-group">
                    <label class="form-label" for="eventEndDate">End Date</label>
                    <input type="date" id="eventEndDate" class="modal-input">
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="eventEndTime">End Time</label>
                    <input type="time" id="eventEndTime" class="modal-input" value="10:00">
                </div>
            </div>
            
            <div class="form-group mb-6">
                <label class="form-label" for="eventLocation">Location</label>
                <input type="text" id="eventLocation" class="modal-input" placeholder="Enter location (optional)">
            </div>
            
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="form-group">
                    <label class="form-label" for="eventType">Event Type</label>
                    <select id="eventType" class="modal-input">
                        <option value="event">Event</option>
                        <option value="task">Task</option>
                        <option value="reminder">Reminder</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="eventPriority">Priority</label>
                    <select id="eventPriority" class="modal-input">
                        <option value="low">Low</option>
                        <option value="medium" selected>Medium</option>
                        <option value="high">High</option>
                    </select>
                </div>
            </div>
            
            <div class="flex items-center mb-6">
                <input type="checkbox" id="isAllDay" class="mr-3">
                <label for="isAllDay" class="form-label mb-0">All-day event</label>
            </div>
            
            <div class="modal-actions">
                <button type="button" class="btn btn-ghost" onclick="hideModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Event</button>
            </div>
        </form>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loadingOverlay" class="loading-overlay">
    <div class="loading-spinner"></div>
</div>

<script>
// Pass PHP data to JavaScript
const CalendarConfig = {
    currentMonth: <?= $currentMonth ?>,
    currentYear: <?= $currentYear ?>,
    selectedDate: '<?= $selectedDate ?>',
    userId: '<?= $_SESSION['user_id'] ?>',
    apiBase: '/Projects/study-tools-website/public/calendar/api/'
};
</script>
