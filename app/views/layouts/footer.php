<?php
/**
 * Footer Component
 */
// Simulate stats - sẽ thay bằng real data sau
$today_todos = 5;
$study_time = '2h 30m';
$streak_days = 7;
?>
<footer class="footer">
    <div class="footer-content">
        <!-- Stats -->
        <div class="stats">
            <div class="stat-item">
                <div class="stat-label">Today's Todos</div>
                <div class="stat-value"><?php echo $today_todos; ?></div>
            </div>
            <div class="stat-item">
                <div class="stat-label">Study Time</div>
                <div class="stat-value"><?php echo $study_time; ?></div>
            </div>
            <div class="stat-item">
                <div class="stat-label">Streak</div>
                <div class="stat-value"><?php echo $streak_days; ?> days</div>
            </div>
        </div>
        
        <!-- Copyright -->
        <div class="copyright">
            &copy; <?php echo date('Y'); ?> StudyHub. All rights reserved.
        </div>
    </div>
</footer>