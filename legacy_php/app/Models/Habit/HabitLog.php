<?php
namespace App\Models\Habit;

use App\Core\Model;

class HabitLog extends Model {

    protected $table = 'habit_list_log';
    protected $primaryKey = 'habit_list_log_id';

    public function toggleToday($habit_list_id) {
        $today = date('Y-m-d');

        $stmt = $this->db->prepare("
            SELECT * FROM habit_list_log 
            WHERE habit_list_id = ? AND log_date = ?
        ");
        $stmt->execute([$habit_list_id, $today]);

        if ($stmt->fetch()) {
            $stmt = $this->db->prepare("DELETE FROM habit_list_log WHERE habit_list_id = ? AND log_date = ?");
            $stmt->execute([$habit_list_id, $today]);
            return false;
        }

        return $this->create([
            'habit_list_log_id' => $this->generateUuid(),
            'habit_list_id' => $habit_list_id,
            'log_date' => $today
        ]);
    }

    public function getStreak($habit_list_id) {
        $stmt = $this->db->prepare("
            SELECT log_date FROM habit_list_log
            WHERE habit_list_id = ?
            ORDER BY log_date DESC
        ");
        $stmt->execute([$habit_list_id]);

        $dates = $stmt->fetchAll(\PDO::FETCH_COLUMN);
        $streak = 0;
        $current = date('Y-m-d');

        // Check if done today or yesterday to continue streak
        $doneToday = false;
        if (!empty($dates) && $dates[0] === $current) {
            $doneToday = true;
        }

        foreach ($dates as $d) {
            if ($d == $current) {
                $streak++;
                $current = date('Y-m-d', strtotime($current . ' -1 day'));
            } else {
                // If we hit a gap, but the gap is "today" and we haven't done it yet, 
                // we might still have a streak from yesterday.
                if ($streak == 0) {
                    $yesterday = date('Y-m-d', strtotime($current . ' -1 day'));
                    if ($d == $yesterday) {
                        $streak++;
                        $current = date('Y-m-d', strtotime($yesterday . ' -1 day'));
                        continue;
                    }
                }
                break;
            }
        }
        return $streak;
    }

    public function isDoneToday($habit_list_id) {
        $today = date('Y-m-d');
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM habit_list_log WHERE habit_list_id = ? AND log_date = ?");
        $stmt->execute([$habit_list_id, $today]);
        return $stmt->fetchColumn() > 0;
    }
}
