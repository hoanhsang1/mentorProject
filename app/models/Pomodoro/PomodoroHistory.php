<?php
namespace App\Models\Pomodoro;

use App\Core\Model;

class PomodoroHistory extends Model
{
    protected $table = 'pomodorohistory';
    protected $primaryKey = 'history_id';
    
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Tạo history record mới
     */
    public function createHistory($data)
    {
        try {
            $data['history_id'] = $this->generateUuid();
            return $this->create($data);
        } catch (\Exception $e) {
            error_log("PomodoroHistory create Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Lấy lịch sử pomodoro của user - SỬA: Chỉ lấy completed
     */
    public function getUserHistory($userId, $limit = 10)
    {
        try {
            $sql = "
                SELECT ph.*, p.title as pomodoro_title 
                FROM {$this->table} ph
                JOIN pomodoro p ON ph.pomodoro_id = p.pomodoro_id
                WHERE p.user_id = ? 
                    AND ph.is_deleted = 0
                    AND ph.status = 'completed'
                ORDER BY ph.start_time DESC
                LIMIT " . (int)$limit;  // CAST trong SQL
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId]);
            
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return is_array($result) ? $result : [];
            
        } catch (\Exception $e) {
            error_log("PomodoroHistory getUserHistory Error: " . $e->getMessage());
            return [];
        }
    }
        
    /**
     * Hoàn thành session
     */
    public function completeSession($historyId, $endTime, $durationMinutes)
    {
        $stmt = $this->db->prepare("
            UPDATE {$this->table} 
            SET end_time = ?, 
                duration_minutes = ?, 
                status = 'completed',
                updated_at = NOW()
            WHERE history_id = ?
        ");
        return $stmt->execute([$endTime, $durationMinutes, $historyId]);
    }
    
    /**
     * Lấy session đang active
     */
    public function getActiveSession($pomodoroId)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table} 
            WHERE pomodoro_id = ? AND status = 'in_progress' AND is_deleted = 0
            ORDER BY start_time DESC
            LIMIT 1
        ");
        $stmt->execute([$pomodoroId]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Thống kê pomodoro
     */
    public function getStats($userId, $dateFrom = null)
    {
        try {
            $sql = "
                SELECT 
                    COUNT(*) as total_sessions,
                    SUM(duration_minutes) as total_minutes,
                    AVG(duration_minutes) as avg_duration,
                    DATE(start_time) as session_date
                FROM {$this->table} ph
                JOIN pomodoro p ON ph.pomodoro_id = p.pomodoro_id
                WHERE p.user_id = ? 
                    AND ph.status = 'completed'  -- CHỈ lấy completed
                    AND ph.is_deleted = 0
            ";
            
            $params = [$userId];
            
            if ($dateFrom) {
                $sql .= " AND ph.start_time >= ?";
                $params[] = $dateFrom;
            }
            
            $sql .= " GROUP BY DATE(ph.start_time) ORDER BY session_date DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            return is_array($result) ? $result : [];
            
        } catch (\Exception $e) {
            error_log("PomodoroHistory getStats Error: " . $e->getMessage());
            return [];
        }
    }
        
    /**
     * Lấy thống kê hôm nay
     */
    public function getTodayStats($userId)
    {
        try {
            $today = date('Y-m-d');
            $sql = "
                SELECT 
                    COUNT(*) as today_sessions,
                    SUM(duration_minutes) as today_minutes
                FROM {$this->table} ph
                JOIN pomodoro p ON ph.pomodoro_id = p.pomodoro_id
                WHERE p.user_id = ? 
                    AND ph.status = 'completed' 
                    AND ph.is_deleted = 0
                    AND DATE(ph.start_time) = ?
            ";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId, $today]);
            return $stmt->fetch(\PDO::FETCH_ASSOC) ?: ['today_sessions' => 0, 'today_minutes' => 0];
            
        } catch (\Exception $e) {
            error_log("PomodoroHistory getTodayStats Error: " . $e->getMessage());
            return ['today_sessions' => 0, 'today_minutes' => 0];
        }
    }
    
    /**
     * Xóa mềm history
     */
    public function softDelete($historyId)
    {
        $stmt = $this->db->prepare("
            UPDATE {$this->table} 
            SET is_deleted = 1, updated_at = NOW() 
            WHERE history_id = ?
        ");
        return $stmt->execute([$historyId]);
    }
}