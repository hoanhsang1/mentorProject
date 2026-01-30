<?php
namespace App\Models\Habit;

use App\Core\Model;


class Habit extends Model
{
    protected $table = 'habit';
    protected $primaryKey = 'habit_id';
    private $selectedColor = '#4a6cf7';


    public function getHabitsWithHistory($user_id, $month = null, $year = null)
    {
        $month = $month ?? date('m');
        $year = $year ?? date('Y');
        
        // Lấy tất cả habitlist items của user - THÊM CỘT COLOR
        $sql = "SELECT hl.habitlist_id, hl.name, hl.daily_target, hl.created_at, 
                    h.user_id, h.habit_id, COALESCE(hl.color, ?) as color
                FROM habitlist hl
                JOIN habit h ON hl.habit_id = h.habit_id
                WHERE h.user_id = ? 
                AND hl.is_deleted = 0
                ORDER BY hl.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$this->selectedColor, $user_id]); // Default color nếu null
        $habits = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        // Thêm thông tin ngày đã hoàn thành từ habitlistlog
        foreach ($habits as &$habit) {
            $sql = "SELECT DAY(date) as day 
                    FROM habitlistlog 
                    WHERE habitlist_id = ? 
                    AND status = 'completed'
                    AND MONTH(date) = ? 
                    AND YEAR(date) = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$habit['habitlist_id'], $month, $year]);
            $completedDays = $stmt->fetchAll(\PDO::FETCH_COLUMN);
            
            $habit['completed_days'] = $completedDays;
            
            // Nếu không có màu trong DB, dùng màu mặc định
            if (empty($habit['color'])) {
                $habit['color'] = $this->getDefaultColor($habit['name']);
            }
        }
        
        return $habits;
    }
    

    private function getDefaultColor($habitName)
    {
        return $this->selectedColor; // Trả về màu mặc định
    }

    public function createHabit($user_id, $title, $frequency = 'daily', $color = '#4a6cf7')
    {
        // 1. First check if user has a habit record
        $sql = "SELECT habit_id FROM habit WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$user_id]);
        $existingHabit = $stmt->fetch();
        
        if (!$existingHabit) {
            // Create habit record for user
            $habitId = $this->generateUuid();
            $sql = "INSERT INTO habit (habit_id, user_id, created_at) VALUES (?, ?, NOW())";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$habitId, $user_id]);
        } else {
            $habitId = $existingHabit['habit_id'];
        }
        
        // 2. Create habitlist item - THÊM CỘT COLOR
        $habitlistId = $this->generateUuid();
        
        // Kiểm tra nếu bảng có cột color
        $checkColumn = $this->db->query("SHOW COLUMNS FROM habitlist LIKE 'color'")->fetch();
        
        if ($checkColumn) {
            $sql = "INSERT INTO habitlist (habitlist_id, name, daily_target, created_at, habit_id, color) 
                    VALUES (?, ?, 1, NOW(), ?, ?)";
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([$habitlistId, $title, $habitId, $color]);
        } else {
            // Nếu chưa có cột color, tạo nó
            $this->db->exec("ALTER TABLE habitlist ADD COLUMN color VARCHAR(7) DEFAULT '#4a6cf7'");
            $sql = "INSERT INTO habitlist (habitlist_id, name, daily_target, created_at, habit_id, color) 
                    VALUES (?, ?, 1, NOW(), ?, ?)";
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([$habitlistId, $title, $habitId, $color]);
        }
        
        return $result ? $habitlistId : false;
    }

    public function deleteHabit($habitlist_id)
    {
        // Soft delete from habitlist
        $sql = "UPDATE habitlist SET is_deleted = 1 WHERE habitlist_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$habitlist_id]);
    }

    public function toggleHabitDay($habitlist_id, $date)
    {
        // Extract year and month from the date
        $dateObj = new \DateTime($date);
        $year = $dateObj->format('Y');
        $month = $dateObj->format('m');
        $day = $dateObj->format('d');
        
        // Check if already completed for this date
        $sql = "SELECT log_id FROM habitlistlog 
                WHERE habitlist_id = ? AND date = ? AND status = 'completed'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$habitlist_id, $date]);
        $existing = $stmt->fetch();
        
        if ($existing) {
            // Delete the completion (toggle off)
            $sql = "DELETE FROM habitlistlog WHERE log_id = ?";
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([$existing['log_id']]);
            return ['status' => false, 'result' => $result, 'day' => $day];
        } else {
            // Create new completion (toggle on)
            $logId = $this->generateUuid();
            $sql = "INSERT INTO habitlistlog (log_id, habitlist_id, date, status, created_at) 
                    VALUES (?, ?, ?, 'completed', NOW())";
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([$logId, $habitlist_id, $date]);
            return ['status' => true, 'result' => $result, 'day' => $day];
        }
    }


    public function generateUuid()
    {
        if (function_exists('com_create_guid') === true) {
            return trim(com_create_guid(), '{}');
        }
        
        // Fallback
        $data = openssl_random_pseudo_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}