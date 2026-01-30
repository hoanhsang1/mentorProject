<?php
namespace App\Models\Calendar;

require_once __DIR__ . '/../../Core/Model.php';
use App\Core\Model;
use PDO;

class Event extends Model {
    protected $table = 'event';
    protected $primaryKey = 'event_id';
    
    /**
     * Lấy events của user theo khoảng thời gian
     */
    public function getUserEvents($userId, $startDate = null, $endDate = null) {
        $sql = "SELECT e.*, c.color as calendar_color 
                FROM {$this->table} e
                JOIN calendar c ON e.calendar_id = c.calendar_id
                WHERE c.user_id = :user_id 
                AND e.is_deleted = 0 
                AND e.status != 'cancelled'
                AND c.is_deleted = 0";
        
        $params = [':user_id' => $userId];
        
        // Thêm điều kiện ngày nếu có
        if ($startDate && $endDate) {
            $sql .= " AND (
                (e.start_at BETWEEN :start_date AND :end_date) OR 
                (e.end_at BETWEEN :start_date AND :end_date) OR 
                (e.start_at <= :start_date AND (e.end_at IS NULL OR e.end_at >= :start_date))
            )";
            $params[':start_date'] = $startDate;
            $params[':end_date'] = $endDate;
        }
        
        $sql .= " ORDER BY e.start_at ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    /**
 * Lấy event theo ID
 */
public function getEventById($eventId)
{
    $sql = "SELECT e.*, c.color as calendar_color 
            FROM {$this->table} e
            JOIN calendar c ON e.calendar_id = c.calendar_id
            WHERE e.event_id = :event_id 
            AND e.is_deleted = 0";
    
    $stmt = $this->db->prepare($sql);
    $stmt->execute([':event_id' => $eventId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Soft delete event
 */
public function softDelete($eventId)
{
    $sql = "UPDATE {$this->table} 
            SET is_deleted = 1, updated_at = NOW() 
            WHERE event_id = ?";
    
    $stmt = $this->db->prepare($sql);
    return $stmt->execute([$eventId]);
}

    /**
     * Tạo event mới
     */
    public function createEvent($userId, $data) {
        // Đảm bảo user có calendar
        $calendarModel = new Calendar();
        $calendar = $calendarModel->getOrCreateCalendar($userId);
        
        $eventId = $this->generateUuid();
        
        $eventData = [
            'event_id' => $eventId,
            'calendar_id' => $calendar['calendar_id'],
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'start_at' => $data['start_at'],
            'end_at' => $data['end_at'] ?? null,
            'is_all_day' => $data['is_all_day'] ?? 0,
            'location' => $data['location'] ?? null,
            'event_type' => $data['event_type'] ?? 'event',
            'status' => $data['status'] ?? 'scheduled',
            'priority' => $data['priority'] ?? 'medium',
            'is_deleted' => 0
        ];
        
        // Thêm repeat_pattern nếu có
        if (isset($data['repeat_pattern'])) {
            $eventData['repeat_pattern'] = $data['repeat_pattern'];
            $eventData['repeat_until'] = $data['repeat_until'] ?? null;
        }
        
        // Thêm task_id nếu có (tích hợp với todo)
        if (isset($data['task_id'])) {
            $eventData['task_id'] = $data['task_id'];
        }
        
        return parent::create($eventData);
    }
    
    /**
     * Lấy event upcoming (sắp diễn ra)
     */
    public function getUpcomingEvents($userId, $limit = 10) {
        $sql = "SELECT e.*, c.color as calendar_color 
                FROM {$this->table} e
                JOIN calendar c ON e.calendar_id = c.calendar_id
                WHERE c.user_id = :user_id 
                AND e.is_deleted = 0 
                AND e.status = 'scheduled'
                AND e.start_at >= NOW()
                ORDER BY e.start_at ASC
                LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_STR);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Thay đổi trạng thái event
     */
    public function changeStatus($eventId, $userId, $status) {
        $sql = "UPDATE {$this->table} e
                JOIN calendar c ON e.calendar_id = c.calendar_id
                SET e.status = :status, e.updated_at = NOW()
                WHERE e.event_id = :event_id 
                AND c.user_id = :user_id
                AND e.is_deleted = 0";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':status' => $status,
            ':event_id' => $eventId,
            ':user_id' => $userId
        ]);
    }
}
?>