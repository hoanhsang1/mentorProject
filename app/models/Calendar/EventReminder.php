<?php
namespace App\Models\Calendar;

require_once __DIR__ . '/../../Core/Model.php';
use App\Core\Model;
use PDO;

class EventReminder extends Model {
    protected $table = 'event_reminder';
    protected $primaryKey = 'reminder_id';
    
    /**
     * Thêm reminder cho event
     */
    public function addReminder($eventId, $remindAt, $remindType = 'notification') {
        $reminderId = $this->generateUuid();
        
        $data = [
            'reminder_id' => $reminderId,
            'event_id' => $eventId,
            'remind_at' => $remindAt,
            'remind_type' => $remindType,
            'is_sent' => 0
        ];
        
        return parent::create($data);
    }
    
    /**
     * Lấy reminders sắp đến hạn
     */
    public function getDueReminders($minutes = 5) {
        $sql = "SELECT er.*, e.title, e.start_at, c.user_id
                FROM {$this->table} er
                JOIN event e ON er.event_id = e.event_id
                JOIN calendar c ON e.calendar_id = c.calendar_id
                WHERE er.is_sent = 0
                AND er.remind_at <= DATE_ADD(NOW(), INTERVAL :minutes MINUTE)
                AND er.remind_at >= NOW()
                AND e.is_deleted = 0
                AND e.status = 'scheduled'";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':minutes', $minutes, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>