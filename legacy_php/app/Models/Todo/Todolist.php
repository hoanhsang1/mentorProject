<?php
namespace App\Models\Todo;
require_once __DIR__ . '/../../Core/Model.php';
use App\Core\Model;
use PDO;

class Todolist extends Model {
    protected $table = 'todolist';
    protected $primaryKey = 'todolist_id';

    public function findTodolistByUser($user_id) {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createTodolist($user_id) {
        $todolist = $this->generateUUID();
        
        $sql = "INSERT INTO {$this->table} (todolist_id, user_id) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$todolist,$user_id]);

    }
}