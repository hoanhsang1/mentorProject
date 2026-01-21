<?php
namespace App\Models\Calendar;

require_once __DIR__ . '/../../Core/Model.php';
use App\Core\Model;
use PDO;
use PDOException;

class Calendar extends Model {
    protected $table = 'calendar';
    protected $primaryKey = 'calendar_id';
    
    /**
     * Lấy calendar của user (chỉ 1 calendar/user)
     */
    public function getUserCalendar($userId) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE user_id = :user_id 
                AND is_deleted = 0 
                LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Lấy hoặc tạo calendar cho user
     */
    public function getOrCreateCalendar($userId) {
        $calendar = $this->getUserCalendar($userId);
        
        if (!$calendar) {
            return $this->createUserCalendar($userId);
        }
        
        return $calendar;
    }
    
    /**
     * Tạo calendar mới cho user
     */
    public function createUserCalendar($userId, $data = []) {
        $calendarId = $this->generateUuid();
        
        $defaultData = [
            'calendar_id' => $calendarId,
            'user_id' => $userId,
            'name' => 'My Calendar',
            'color' => '#4a6cf7',
            'description' => 'Personal calendar',
            'is_deleted' => 0
        ];
        
        $data = array_merge($defaultData, $data);
        
        try {
            return parent::create($data);
        } catch (PDOException $e) {
            // Nếu user đã có calendar (duplicate entry), return calendar hiện có
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return $this->getUserCalendar($userId);
            }
            throw $e;
        }
    }
    
    /**
     * Cập nhật calendar của user
     */
    public function updateUserCalendar($userId, $data) {
        $sql = "UPDATE {$this->table} 
                SET name = :name, 
                    color = :color, 
                    description = :description,
                    updated_at = NOW()
                WHERE user_id = :user_id 
                AND is_deleted = 0";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':name' => $data['name'] ?? 'My Calendar',
            ':color' => $data['color'] ?? '#4a6cf7',
            ':description' => $data['description'] ?? null,
            ':user_id' => $userId
        ]);
    }
    
    /**
     * Xóa mềm calendar của user
     */
    public function softDeleteUserCalendar($userId) {
        $sql = "UPDATE {$this->table} 
                SET is_deleted = 1, updated_at = NOW() 
                WHERE user_id = :user_id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':user_id' => $userId]);
    }
    
    /**
     * Khôi phục calendar đã xóa
     */
    public function restoreUserCalendar($userId) {
        $sql = "UPDATE {$this->table} 
                SET is_deleted = 0, updated_at = NOW() 
                WHERE user_id = :user_id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':user_id' => $userId]);
    }
    
    /**
     * Kiểm tra user có calendar không
     */
    public function userHasCalendar($userId) {
        $sql = "SELECT COUNT(*) as count 
                FROM {$this->table} 
                WHERE user_id = :user_id 
                AND is_deleted = 0";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }
    
    /**
     * Lấy thông tin calendar với thống kê event
     */
    public function getCalendarWithStats($userId) {
        $sql = "SELECT 
                    c.*,
                    COUNT(e.event_id) as total_events,
                    SUM(CASE WHEN e.status = 'scheduled' THEN 1 ELSE 0 END) as scheduled_events,
                    SUM(CASE WHEN e.status = 'completed' THEN 1 ELSE 0 END) as completed_events,
                    SUM(CASE WHEN e.start_at >= CURDATE() THEN 1 ELSE 0 END) as upcoming_events
                FROM {$this->table} c
                LEFT JOIN event e ON c.calendar_id = e.calendar_id 
                    AND e.is_deleted = 0
                WHERE c.user_id = :user_id 
                    AND c.is_deleted = 0
                GROUP BY c.calendar_id
                LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>