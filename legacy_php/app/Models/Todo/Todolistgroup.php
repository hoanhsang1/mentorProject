<?php
namespace App\Models\Todo;
require_once __DIR__ . '/../../Core/Model.php';
require_once __DIR__ . '/Todolist.php';
use App\Models\Todo\Todolist;
use FFI\Exception;
use PDO;
class Todolistgroup extends Todolist {
    protected $table = 'todolistgroup';
    protected $primaryKey = 'group_id';

    public function getAllGroupById($todolist) {
        $sql = "SELECT * FROM {$this->table} WHERE todolist_id = ? && is_deleted = 0 ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$todolist]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createGroup($todolistID, $title) {
        $groupId = $this->generateUUID();

        $sql = "INSERT INTO {$this->table} (todolist_id, title, group_id) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $ok = $stmt->execute([$todolistID, $title, $groupId]);

        if (!$ok) return false;

        return [
            "group_id" => $groupId,
            "title" => $title,
            "todolist_id" => $todolistID
        ];
    }

}