<?php
namespace App\Controllers\Api;

use App\Models\Pomodoro\Pomodoro;
use App\Models\Pomodoro\PomodoroHistory;

class PomodoroController
{
    private $pomodoroModel;
    private $historyModel;
    
    public function __construct()
    {
        // Start session if not started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $this->pomodoroModel = new Pomodoro();
        $this->historyModel = new PomodoroHistory();
    }
    
    /**
     * Lấy thông tin pomodoro của user
     */
    public function getPomodoro()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->json(false, "Unauthorized");
            return;
        }
        
        try {
            $pomodoro = $this->pomodoroModel->getUserPomodoro($_SESSION['user_id']);
            
            if (!$pomodoro) {
                $this->json(false, "Pomodoro not found");
                return;
            }
            
            $this->json(true, null, ["pomodoro" => $pomodoro]);
            
        } catch (\Exception $e) {
            $this->json(false, "Error: " . $e->getMessage());
        }
    }
    
    /**
     * Bắt đầu pomodoro session
     */
    public function startSession()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->json(false, "Unauthorized");
            return;
        }
        
        try {
            $studyTopic = trim($_POST['study_topic'] ?? '');
            
            // Lấy pomodoro của user
            $pomodoro = $this->pomodoroModel->getUserPomodoro($_SESSION['user_id']);
            
            // Tạo history record
            $historyData = [
                'pomodoro_id' => $pomodoro['pomodoro_id'],
                'start_time' => date('Y-m-d H:i:s'),
                'study_topic' => $studyTopic ?: null,
                'status' => 'in_progress'
            ];
            
            $history = $this->historyModel->createHistory($historyData);
            
            // Cập nhật trạng thái pomodoro
            $this->pomodoroModel->updateStatus($pomodoro['pomodoro_id'], 'running');
            $this->pomodoroModel->updateCurrentSession($pomodoro['pomodoro_id'], 'work');
            
            // Lấy lại data mới nhất
            $updatedPomodoro = $this->pomodoroModel->getUserPomodoro($_SESSION['user_id']);
            $activeSession = $this->historyModel->getActiveSession($pomodoro['pomodoro_id']);
            
            $this->json(true, null, [
                'pomodoro' => $updatedPomodoro,
                'session' => $activeSession
            ]);
            
        } catch (\Exception $e) {
            $this->json(false, "Error: " . $e->getMessage());
        }
    }
    
    /**
     * Tạm dừng session
     */
    public function pauseSession()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->json(false, "Unauthorized");
            return;
        }
        
        try {
            $pomodoro = $this->pomodoroModel->getUserPomodoro($_SESSION['user_id']);
            
            if ($pomodoro['status'] !== 'running') {
                $this->json(false, "No active session to pause");
                return;
            }
            
            $this->pomodoroModel->updateStatus($pomodoro['pomodoro_id'], 'paused');
            
            $this->json(true, null, [
                'pomodoro' => $this->pomodoroModel->getUserPomodoro($_SESSION['user_id'])
            ]);
            
        } catch (\Exception $e) {
            $this->json(false, "Error: " . $e->getMessage());
        }
    }
    
    /**
     * Tiếp tục session
     */
    public function resumeSession()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->json(false, "Unauthorized");
            return;
        }
        
        try {
            $pomodoro = $this->pomodoroModel->getUserPomodoro($_SESSION['user_id']);
            
            if ($pomodoro['status'] !== 'paused') {
                $this->json(false, "No paused session to resume");
                return;
            }
            
            $this->pomodoroModel->updateStatus($pomodoro['pomodoro_id'], 'running');
            
            $this->json(true, null, [
                'pomodoro' => $this->pomodoroModel->getUserPomodoro($_SESSION['user_id'])
            ]);
            
        } catch (\Exception $e) {
            $this->json(false, "Error: " . $e->getMessage());
        }
    }
    
    /**
     * Kết thúc session
     */
    public function endSession()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->json(false, "Unauthorized");
            return;
        }
        
        try {
            $completed = isset($_POST['completed']) && $_POST['completed'] === 'true';
            
            $pomodoro = $this->pomodoroModel->getUserPomodoro($_SESSION['user_id']);
            $activeSession = $this->historyModel->getActiveSession($pomodoro['pomodoro_id']);
            
            if (!$activeSession) {
                $this->json(false, "No active session found");
                return;
            }
            
            $endTime = date('Y-m-d H:i:s');
            $startTime = new \DateTime($activeSession['start_time']);
            $endTimeObj = new \DateTime($endTime);
            $duration = $endTimeObj->getTimestamp() - $startTime->getTimestamp();
            $durationMinutes = floor($duration / 60);
            
            // Cập nhật history
            $this->historyModel->completeSession(
                $activeSession['history_id'],
                $endTime,
                $durationMinutes
            );
            
            // Cập nhật pomodoro
            if ($completed && $pomodoro['current_session'] === 'work') {
                $this->pomodoroModel->incrementSessions($pomodoro['pomodoro_id']);
            }
            
            $this->pomodoroModel->updateStatus($pomodoro['pomodoro_id'], 'stopped');
            
            $this->json(true, null, [
                'pomodoro' => $this->pomodoroModel->getUserPomodoro($_SESSION['user_id']),
                'session' => [
                    'duration_minutes' => $durationMinutes,
                    'completed' => $completed
                ]
            ]);
            
        } catch (\Exception $e) {
            $this->json(false, "Error: " . $e->getMessage());
        }
    }
    
    /**
     * Chuyển đổi session (work <-> break)
     */
    public function switchSession()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->json(false, "Unauthorized");
            return;
        }
        
        try {
            $pomodoro = $this->pomodoroModel->getUserPomodoro($_SESSION['user_id']);
            
            $newSession = $pomodoro['current_session'] === 'work' ? 'break' : 'work';
            $this->pomodoroModel->updateCurrentSession($pomodoro['pomodoro_id'], $newSession);
            
            // Nếu chuyển từ break sang work, tăng session count
            if ($pomodoro['current_session'] === 'break' && $newSession === 'work') {
                $this->pomodoroModel->incrementSessions($pomodoro['pomodoro_id']);
            }
            
            $this->json(true, null, [
                'pomodoro' => $this->pomodoroModel->getUserPomodoro($_SESSION['user_id']),
                'previous_session' => $pomodoro['current_session'],
                'new_session' => $newSession
            ]);
            
        } catch (\Exception $e) {
            $this->json(false, "Error: " . $e->getMessage());
        }
    }
    
    /**
     * Cập nhật cài đặt
     */
    public function updateSettings()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->json(false, "Unauthorized");
            return;
        }
        
        try {
            $workDuration = (int)($_POST['work_duration'] ?? 25);
            $breakDuration = (int)($_POST['break_duration'] ?? 5);
            
            if ($workDuration < 1 || $breakDuration < 1) {
                $this->json(false, "Duration must be at least 1 minute");
                return;
            }
            
            $pomodoro = $this->pomodoroModel->getUserPomodoro($_SESSION['user_id']);
            $this->pomodoroModel->updateDurations($pomodoro['pomodoro_id'], $workDuration, $breakDuration);
            
            $this->json(true, null, [
                'pomodoro' => $this->pomodoroModel->getUserPomodoro($_SESSION['user_id'])
            ]);
            
        } catch (\Exception $e) {
            $this->json(false, "Error: " . $e->getMessage());
        }
    }
    
    /**
     * Lấy lịch sử
     */
    public function getHistory()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->json(false, "Unauthorized");
            return;
        }
        
        try {
            $limit = (int)($_GET['limit'] ?? 10);
            $history = $this->historyModel->getUserHistory($_SESSION['user_id'], $limit);
            
            $this->json(true, null, [
                'history' => $history
            ]);
            
        } catch (\Exception $e) {
            $this->json(false, "Error: " . $e->getMessage());
        }
    }
    
    /**
     * Lấy thống kê
     */
    public function getStats()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->json(false, "Unauthorized");
            return;
        }
        
        try {
            $days = (int)($_GET['days'] ?? 7);
            $dateFrom = date('Y-m-d', strtotime("-$days days"));
            $stats = $this->historyModel->getStats($_SESSION['user_id'], $dateFrom);
            
            // Tính tổng
            $totalSessions = 0;
            $totalMinutes = 0;
            
            foreach ($stats as $stat) {
                $totalSessions += (int)($stat['total_sessions'] ?? 0);
                $totalMinutes += (int)($stat['total_minutes'] ?? 0);
            }
            
            $this->json(true, null, [
                'stats' => $stats,
                'summary' => [
                    'total_sessions' => $totalSessions,
                    'total_minutes' => $totalMinutes,
                    'total_hours' => round($totalMinutes / 60, 1),
                    'days_analyzed' => $days
                ]
            ]);
            
        } catch (\Exception $e) {
            $this->json(false, "Error: " . $e->getMessage());
        }
    }
    
    /**
     * JSON response helper - GIỐNG với TodoController
     */
    private function json($success, $error = null, $extra = [])
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(array_merge([
            "success" => $success,
            "error" => $error
        ], $extra));
        exit;
    }
}