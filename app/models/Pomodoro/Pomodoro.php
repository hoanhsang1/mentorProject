<?php
namespace App\Models\Pomodoro;

use App\Core\Model;

class Pomodoro extends Model
{
    protected $table = 'pomodoro';
    protected $primaryKey = 'pomodoro_id';
    
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Lấy pomodoro session của user
     */
    public function getUserPomodoro($userId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM {$this->table} 
                WHERE user_id = ? 
                ORDER BY created_at DESC 
                LIMIT 1
            ");
            $stmt->execute([$userId]);
            
            $pomodoro = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            // Nếu chưa có, tạo mới
            if (!$pomodoro) {
                $pomodoroId = $this->generateUuid();
                $stmt = $this->db->prepare("
                    INSERT INTO {$this->table} 
                    (pomodoro_id, user_id, title, status, work_duration, break_duration, current_session) 
                    VALUES (?, ?, 'Pomodoro Session', 'stopped', 25, 5, 'work')
                ");
                $stmt->execute([$pomodoroId, $userId]);
                
                // Lấy lại vừa tạo
                return $this->getById($pomodoroId);
            }
            
            return $pomodoro;
            
        } catch (\Exception $e) {
            error_log("Pomodoro Model Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Lấy by ID
     */
    public function getById($pomodoroId)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE pomodoro_id = ?");
        $stmt->execute([$pomodoroId]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Cập nhật trạng thái pomodoro
     */
    public function updateStatus($pomodoroId, $status)
    {
        $stmt = $this->db->prepare("
            UPDATE {$this->table} 
            SET status = ?, updated_at = NOW() 
            WHERE pomodoro_id = ?
        ");
        return $stmt->execute([$status, $pomodoroId]);
    }
    
    /**
     * Cập nhật sessions completed
     */
    public function incrementSessions($pomodoroId)
    {
        $stmt = $this->db->prepare("
            UPDATE {$this->table} 
            SET sessions_completed = sessions_completed + 1, 
                updated_at = NOW() 
            WHERE pomodoro_id = ?
        ");
        return $stmt->execute([$pomodoroId]);
    }
    
    /**
     * Cập nhật thời gian làm việc/nghỉ
     */
    public function updateDurations($pomodoroId, $workDuration, $breakDuration)
    {
        $stmt = $this->db->prepare("
            UPDATE {$this->table} 
            SET work_duration = ?, break_duration = ?, updated_at = NOW() 
            WHERE pomodoro_id = ?
        ");
        return $stmt->execute([$workDuration, $breakDuration, $pomodoroId]);
    }
    
    /**
     * Cập nhật session hiện tại
     */
    public function updateCurrentSession($pomodoroId, $session)
    {
        $stmt = $this->db->prepare("
            UPDATE {$this->table} 
            SET current_session = ?, updated_at = NOW() 
            WHERE pomodoro_id = ?
        ");
        return $stmt->execute([$session, $pomodoroId]);
    }
    
    /**
     * Cập nhật tiêu đề
     */
    public function updateTitle($pomodoroId, $title)
    {
        $stmt = $this->db->prepare("
            UPDATE {$this->table} 
            SET title = ?, updated_at = NOW() 
            WHERE pomodoro_id = ?
        ");
        return $stmt->execute([$title, $pomodoroId]);
    }
    
    /**
     * Reset sessions completed
     */
    public function resetSessions($pomodoroId)
    {
        $stmt = $this->db->prepare("
            UPDATE {$this->table} 
            SET sessions_completed = 0, updated_at = NOW() 
            WHERE pomodoro_id = ?
        ");
        return $stmt->execute([$pomodoroId]);
    }
}