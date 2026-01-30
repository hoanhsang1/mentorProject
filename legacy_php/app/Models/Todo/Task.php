<?php
namespace App\Models\Todo;
require_once __DIR__ . '/../../Core/Model.php';
require_once __DIR__ . '/Todolistgroup.php';
use App\Models\Todo\Todolistgroup;
use FFI\Exception;
use PDO;
class Task extends Todolistgroup {
    protected $table = 'task';
    protected $primaryKey = 'task_id';

    public function getAllTaskByGroupId($id) {
        $sql = "SELECT * FROM {$this->table} WHERE group_id = ? && is_deleted = 0 ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTaskByTaskId($id) {
        $sql = "SELECT * FROM {$this->table} WHERE task_id = ? && is_deleted = 0 ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createTask($data) {
        try {
            error_log("Task model createTask called with data: " . print_r($data, true));
            
            $taskId = $this->generateUuid();
            
            $sql = "INSERT INTO task 
                    (task_id, title, `description`, priority, `status`, deadline, group_id, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
            
            error_log("SQL: " . $sql);
            
            $stmt = $this->db->prepare($sql);
            
            // Xử lý deadline một lần duy nhất
            $deadline = null;
            if (!empty($data['due_date'])) {
                $dateInput = $data['due_date'];
                if (strtotime($dateInput)) {
                    $deadline = date('Y-m-d', strtotime($dateInput));
                }
            } elseif (!empty($data['deadline'])) {
                $dateInput = $data['deadline'];
                if (strtotime($dateInput)) {
                    $deadline = date('Y-m-d', strtotime($dateInput));
                }
            }
            
            // SỬA: Dùng $deadline đã xử lý
            $params = [
                $taskId,
                $data['task'] ?? $data['title'] ?? '',  // title
                $data['description'] ?? '',             // description
                $data['priority'] ?? 'medium',          // priority
                $data['status'] ?? 'pending',           // status
                $deadline,                              // SỬA: dùng biến $deadline đã xử lý
                $data['group_id'] ?? null               // group_id
            ];
            
            error_log("Params: " . print_r($params, true));
            
            $ok = $stmt->execute($params);
            
            error_log("Execute result: " . ($ok ? 'true' : 'false'));
            
            if (!$ok) {
                $errorInfo = $stmt->errorInfo();
                error_log("PDO error: " . print_r($errorInfo, true));
                return false;
            }

            return [
                "task_id" => $taskId,
                "title" => $params[1],                      // title
                "task" => $params[1],                       // task
                "description" => $params[2],                // description
                "priority" => $params[3],                   // priority
                "deadline" => $deadline,                    // SỬA: dùng $deadline
                "due_date" => $deadline,                    // SỬA: dùng $deadline
                "status" => $params[4],                     // status
                "group_id" => $params[6],                   // group_id
                "created_at" => date('Y-m-d H:i:s')
            ];
            
        } catch (\Exception $e) {
            error_log("Exception in Task::createTask: " . $e->getMessage());
            return false;
        }
    }

    public function toggleStatus($id) {
        $task = $this->getTaskByTaskId($id);

        $newStatus = $task['status'] === 'completed' ? 'pending' : 'completed';

        $ok = $this->update($id, ['status' => $newStatus]);

        if (!$ok) return false;

        return $this->getTaskByTaskId($id); // trả task mới
    }



    public function markOverdueByGroup($groupId) {
        $sql = "
            UPDATE task
            SET status = 'overdue'
            WHERE group_id = ?
            AND deadline < CURDATE()
            AND status NOT IN ('completed', 'overdue')
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$groupId]);
    }

}