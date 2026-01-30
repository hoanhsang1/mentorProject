<?php
namespace App\Models\Habit;

use App\Core\Model;

class HabitList extends Model {

    protected $table = 'habit_list';
    protected $primaryKey = 'habit_list_id';

    public function getByHabit($habit_id) {
        $sql = "SELECT hl.*, 
                (SELECT COUNT(*) FROM habit_list_log l WHERE l.habit_list_id = hl.habit_list_id) as total_done
                FROM habit_list hl
                WHERE hl.habit_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$habit_id]);
        return $stmt->fetchAll();
    }

    public function createItem($habit_id, $title) {
        return $this->create([
            'habit_list_id' => $this->generateUuid(),
            'habit_id' => $habit_id,
            'title' => $title,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
}
