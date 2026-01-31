
document.addEventListener('DOMContentLoaded', function () {
    // 1. Fetch data from the window variable (passed from Django)
    const stats = window.dashboardStats || {};
    const activities = window.dashboardActivities || [];

    // 2. Render Statistics and User Info
    const elementsToUpdate = {
        'user-welcome-name': stats.user_name || 'Guest',
        'user-plan-status': stats.user_role || 'Free',
        'stat-study-time': stats.study_time || '0m',
        'stat-completion': (stats.completion_rate || 0) + '%',
        'stat-streak': (stats.streak || 0) + ' Days',
        'stat-pending-todos': stats.pending_todos || 0,
        'stat-upcoming-events': stats.upcoming_events || 0,
        'stat-active-habits': stats.active_habits || 0,
        'stat-flashcards-due': stats.flashcards_due || 0
    };

    for (const [id, value] of Object.entries(elementsToUpdate)) {
        const el = document.getElementById(id);
        if (el) {
            el.textContent = value;
            // Add a small fade-in animation
            el.style.opacity = '0';
            setTimeout(() => {
                el.style.transition = 'opacity 0.5s ease-in';
                el.style.opacity = '1';
            }, 50);
        }
    }

    // 3. Render Activities Timeline
    const activityContainer = document.getElementById('recent-activity-timeline');
    if (activityContainer && activities.length > 0) {
        activityContainer.innerHTML = ''; // Clear loading/placeholder

        activities.forEach(activity => {
            const activityHtml = `
                <div class="relative flex items-start gap-4 z-10">
                    <div class="w-12 h-12 rounded-full bg-bg-input flex items-center justify-center border-4 border-white shadow-sm flex-shrink-0">
                        <span class="text-lg">${activity.icon}</span>
                    </div>
                    <div class="flex-1 bg-bg-input p-4 rounded-xl hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start mb-1">
                            <h4 class="font-bold text-text">${activity.title}</h4>
                            <span class="text-xs text-text-secondary">${activity.time_display}</span>
                        </div>
                        ${activity.desc ? `<p class="text-text-secondary text-sm">${activity.desc}</p>` : ''}
                    </div>
                </div>
            `;
            activityContainer.insertAdjacentHTML('beforeend', activityHtml);
        });
    } else if (activityContainer) {
        activityContainer.innerHTML = '<div class="text-center py-8 text-text-secondary italic">No recent activity found.</div>';
    }
});
