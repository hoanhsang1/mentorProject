<?php
namespace App\Controllers\Api;

use App\Core\Controller;
use App\Models\Habit\Habit;
use App\Models\Habit\HabitList;
use App\Models\Habit\HabitLog;

class HabitController extends Controller
{
    private $habitModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            $this->json(false, "Unauthorized");
            exit;
        }
        
        $this->habitModel = new Habit();
    }

    public function getHabits()
    {
        $month = $_GET['month'] ?? date('m');
        $year = $_GET['year'] ?? date('Y');
        
        $habits = $this->habitModel->getHabitsWithHistory($_SESSION['user_id'], $month, $year);
        
        $this->json(true, null, [
            'habits' => $habits,
            'current_month' => date('F', mktime(0, 0, 0, $month, 1, $year)),
            'year' => $year,
            'month' => $month,
            'days_in_month' => date('t', mktime(0, 0, 0, $month, 1, $year)),
            'today' => date('j')
        ]);
    }

    public function createHabit()
    {
        // Nhận dữ liệu từ JSON
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        
        $title = $data['title'] ?? '';
        $color = $data['color'] ?? '#4a6cf7'; // Lấy màu từ request
        
        if (empty($title)) {
            $this->json(false, "Title is required");
            return;
        }
        
        // Validate color
        if (!preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $color)) {
            $color = '#4a6cf7'; // Màu mặc định nếu không hợp lệ
        }
        
        $result = $this->habitModel->createHabit($_SESSION['user_id'], $title, 'daily', $color);
        
        if ($result) {
            $this->json(true, "Habit created successfully", ['habitlist_id' => $result]);
        } else {
            $this->json(false, "Failed to create habit");
        }
    }

    public function updateHabit()
    {
        // Nhận dữ liệu từ JSON
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        
        $habitlist_id = $data['habit_id'] ?? '';
        $title = $data['title'] ?? '';
        $color = $data['color'] ?? '#4a6cf7';
        $daily_target = $data['daily_target'] ?? 1;
        
        if (empty($habitlist_id) || empty($title)) {
            $this->json(false, "Habit ID and title are required");
            return;
        }
        
        // Verify ownership
        $sql = "SELECT h.user_id 
                FROM habitlist hl 
                JOIN habit h ON hl.habit_id = h.habit_id 
                WHERE hl.habitlist_id = ?";
        $stmt = $this->habitModel->query($sql, [$habitlist_id]);
        $habit = $stmt->fetch();
        
        if (!$habit || $habit['user_id'] !== $_SESSION['user_id']) {
            $this->json(false, "Unauthorized");
            return;
        }
        
        // Update habit
        $sql = "UPDATE habitlist 
                SET name = ?, color = ?, daily_target = ?, updated_at = NOW() 
                WHERE habitlist_id = ?";
        
        $stmt = $this->habitModel->query($sql, [$title, $color, $daily_target, $habitlist_id]);
        
        if ($stmt) {
            $this->json(true, "Habit updated successfully");
        } else {
            $this->json(false, "Failed to update habit");
        }
    }

    public function deleteHabit()
    {
        // Nhận dữ liệu từ JSON
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        
        $habitlist_id = $data['habit_id'] ?? ''; // Thực chất là habitlist_id
        
        if (empty($habitlist_id)) {
            $this->json(false, "Habit ID is required");
            return;
        }
        
        // Verify ownership (through habit table)
        $sql = "SELECT h.user_id 
                FROM habitlist hl 
                JOIN habit h ON hl.habit_id = h.habit_id 
                WHERE hl.habitlist_id = ?";
        $stmt = $this->habitModel->query($sql, [$habitlist_id]);
        $habit = $stmt->fetch();
        
        if (!$habit || $habit['user_id'] !== $_SESSION['user_id']) {
            $this->json(false, "Unauthorized");
            return;
        }
        
        $result = $this->habitModel->deleteHabit($habitlist_id);
        
        if ($result) {
            $this->json(true, "Habit deleted successfully");
        } else {
            $this->json(false, "Failed to delete habit");
        }
    }

    public function toggleHabit()
    {
        // Nhận dữ liệu từ JSON
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        
        $habitlist_id = $data['habit_id'] ?? ''; // Thực chất là habitlist_id
        $date = $data['date'] ?? date('Y-m-d');
        
        if (empty($habitlist_id)) {
            $this->json(false, "Habit ID is required");
            return;
        }
        
        // Verify ownership
        $sql = "SELECT h.user_id 
                FROM habitlist hl 
                JOIN habit h ON hl.habit_id = h.habit_id 
                WHERE hl.habitlist_id = ?";
        $stmt = $this->habitModel->query($sql, [$habitlist_id]);
        $habit = $stmt->fetch();
        
        if (!$habit || $habit['user_id'] !== $_SESSION['user_id']) {
            $this->json(false, "Unauthorized");
            return;
        }
        
        $result = $this->habitModel->toggleHabitDay($habitlist_id, $date);
        
        if ($result['result']) {
            $this->json(true, null, ['status' => $result['status']]);
        } else {
            $this->json(false, "Failed to toggle habit");
        }
    }

    public function getHabitStats()
    {
        $month = $_GET['month'] ?? date('m');
        $year = $_GET['year'] ?? date('Y');
        
        $habits = $this->habitModel->getHabitsWithHistory($_SESSION['user_id'], $month, $year);
        
        $stats = [
            'total_habits' => count($habits),
            'total_completions' => 0,
            'completion_rate' => 0,
            'streaks' => []
        ];
        
        foreach ($habits as $habit) {
            $stats['total_completions'] += count($habit['completed_days'] ?? []);
        }
        
        if ($stats['total_habits'] > 0) {
            $daysInMonth = date('t', mktime(0, 0, 0, $month, 1, $year));
            $totalPossible = $stats['total_habits'] * $daysInMonth;
            $stats['completion_rate'] = $totalPossible > 0 ? 
                round(($stats['total_completions'] / $totalPossible) * 100, 1) : 0;
        }
        
        $this->json(true, null, $stats);
    }
}