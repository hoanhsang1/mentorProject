<?php
namespace App\Controllers\Api;

use App\Core\Controller;
use App\Models\User;
use App\Models\Pomodoro\Pomodoro;
use App\Models\Pomodoro\PomodoroHistory;

class AdminController extends Controller
{
    private $userModel;
    private $pomodoroModel;
    private $historyModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $this->userModel = new User();
        $this->pomodoroModel = new Pomodoro();
        $this->historyModel = new PomodoroHistory();
    }

    public function handle()
    {
        // DEBUG: Kiểm tra đầu vào
        error_log("=== ADMIN API CALL ===");
        error_log("Action: " . ($_GET['action'] ?? $_POST['action'] ?? 'none'));
        error_log("Session user_id: " . ($_SESSION['user_id'] ?? 'none'));
        error_log("Session role: " . ($_SESSION['role'] ?? 'none'));
        
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            error_log("ACCESS DENIED: Not admin");
            $this->json(false, 'Unauthorized access');
            return;
        }

        $action = $_GET['action'] ?? $_POST['action'] ?? '';

        switch ($action) {
            case 'get_stats':
                $this->getDashboardStats();
                break;
            case 'get_users':
                $this->getUsers();
                break;
            case 'get_user_detail':
                $this->getUserDetail();
                break;
            case 'reset_password':
                $this->resetPassword();
                break;
            case 'toggle_user_status':
                $this->toggleUserStatus();
                break;
            case 'delete_user':
                $this->deleteUser();
                break;
            case 'get_pomodoro_stats':
                $this->getPomodoroStats();
                break;
            case 'get_user_activity':
                $this->getUserActivity();
                break;
            case 'get_daily_activity':
                $this->getDailyActivity();
                break;
            case 'get_activity_table':
                $this->getActivityTable();
                break;
            default:
                $this->json(false, 'Invalid action');
        }
    }

    private function getDashboardStats()
    {
        error_log("getDashboardStats called");
        
        try {
            // Tổng số người dùng
            $totalUsers = $this->userModel->getTotalUsers();
            error_log("Total users: " . $totalUsers);
            
            // Người dùng mới trong 30 ngày
            $newUsers30Days = $this->userModel->getNewUsersLastDays(30);
            error_log("New users 30 days: " . $newUsers30Days);
            
            // Tổng thời gian học Pomodoro
            $totalStudyTime = $this->historyModel->getTotalStudyTime();
            error_log("Total study time (minutes): " . $totalStudyTime);
            
            // Tổng số session Pomodoro
            $totalSessions = $this->historyModel->getTotalSessions();
            error_log("Total sessions: " . $totalSessions);
            
            // Người dùng active (có activity trong 7 ngày)
            $activeUsers = $this->getActiveUsersCount(7);
            error_log("Active users: " . $activeUsers);
            
            // Thống kê theo ngày (7 ngày gần nhất)
            $dailyStats = $this->historyModel->getDailyStats(7);
            error_log("Daily stats count: " . count($dailyStats));

            $response = [
                'total_users' => (int)$totalUsers,
                'new_users_30_days' => (int)$newUsers30Days,
                'total_study_hours' => round($totalStudyTime / 60, 1),
                'total_sessions' => (int)$totalSessions,
                'active_users' => (int)$activeUsers,
                'daily_stats' => $dailyStats
            ];
            
            error_log("Response ready, sending...");
            $this->json(true, null, $response);
            
        } catch (\Exception $e) {
            error_log("Error in getDashboardStats: " . $e->getMessage());
            error_log("Trace: " . $e->getTraceAsString());
            $this->json(false, 'Error: ' . $e->getMessage());
        }
    }

    private function getActiveUsersCount($days)
    {
        try {
            $date = date('Y-m-d', strtotime("-$days days"));
            $sql = "SELECT COUNT(DISTINCT p.user_id) as count 
                    FROM pomodoro p 
                    JOIN pomodorohistory ph ON p.pomodoro_id = ph.pomodoro_id
                    WHERE ph.start_time >= :date 
                        AND ph.status = 'completed' 
                        AND ph.is_deleted = 0";
            
            $stmt = $this->historyModel->query($sql, [':date' => $date]);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $result['count'] ?? 0;
            
        } catch (\Exception $e) {
            error_log("Error in getActiveUsersCount: " . $e->getMessage());
            return 0;
        }
    }

    private function getUsers()
    {
        error_log("getUsers called");
        
        try {
            $page = (int)($_GET['page'] ?? 1);
            $limit = (int)($_GET['limit'] ?? 10);
            $search = $_GET['search'] ?? '';
            
            error_log("Params - page: $page, limit: $limit, search: '$search'");
            
            $users = $this->userModel->getAllUsers($page, $limit, $search,true);
            $total = $this->userModel->countUsers($search);
            
            error_log("Found users: " . count($users));
            error_log("Total users: " . $total);
            
            $this->json(true, null, [
                'users' => $users,
                'pagination' => [
                    'page' => $page,
                    'limit' => $limit,
                    'total' => $total,
                    'total_pages' => ceil($total / $limit)
                ]
            ]);
            
        } catch (\Exception $e) {
            error_log("Error in getUsers: " . $e->getMessage());
            $this->json(false, 'Error: ' . $e->getMessage());
        }
    }

    private function getUserDetail()
    {
        error_log("getUserDetail called");
        
        try {
            $userId = $_GET['user_id'] ?? '';
            
            if (empty($userId)) {
                error_log("User ID is empty");
                $this->json(false, 'User ID is required');
                return;
            }
            
            error_log("Getting detail for user: " . $userId);
            
            $user = $this->userModel->findById($userId);
            
            if (!$user) {
                error_log("User not found: " . $userId);
                $this->json(false, 'User not found');
                return;
            }
            
            // Lấy thông tin Pomodoro của user
            $pomodoroStats = $this->historyModel->getUserPomodoroStats($userId);
            error_log("Pomodoro stats: " . json_encode($pomodoroStats));
            
            // Lấy activity gần nhất
            $recentActivity = $this->historyModel->getUserActivity($userId, 10);
            error_log("Recent activity count: " . count($recentActivity));
            
            $this->json(true, null, [
                'user' => $user,
                'pomodoro_stats' => $pomodoroStats,
                'recent_activity' => $recentActivity
            ]);
            
        } catch (\Exception $e) {
            error_log("Error in getUserDetail: " . $e->getMessage());
            $this->json(false, 'Error: ' . $e->getMessage());
        }
    }

    private function resetPassword()
    {
        error_log("resetPassword called");
        
        try {
            $userId = $_POST['user_id'] ?? '';
            
            if (empty($userId)) {
                error_log("User ID is empty");
                $this->json(false, 'User ID is required');
                return;
            }
            
            // Tạo password mới
            $newPassword = bin2hex(random_bytes(4)); // 8 ký tự hex
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            
            error_log("Resetting password for user: " . $userId);
            error_log("New password (plain): " . $newPassword);
            
            $result = $this->userModel->resetPassword($userId, $hashedPassword);
            
            if ($result) {
                error_log("Password reset successful");
                $this->json(true, null, [
                    'message' => 'Password reset successfully',
                    'new_password' => $newPassword
                ]);
            } else {
                error_log("Password reset failed");
                $this->json(false, 'Failed to reset password');
            }
        } catch (\Exception $e) {
            error_log("Error in resetPassword: " . $e->getMessage());
            $this->json(false, 'Error: ' . $e->getMessage());
        }
    }

    private function toggleUserStatus()
    {
        error_log("toggleUserStatus called");
        
        try {
            $userId = $_POST['user_id'] ?? '';
            
            if (empty($userId)) {
                error_log("User ID is empty");
                $this->json(false, 'User ID is required');
                return;
            }
            
            $user = $this->userModel->findById($userId,true);
            if (!$user) {
                error_log("User not found: " . $userId);
                $this->json(false, 'User not found');
                return;
            }
            
            // Toggle trạng thái (0 = active, 1 = locked)
            $newStatus = $user['is_deleted'] ? 0 : 1;
            error_log("Toggling user status: " . $userId . " from " . $user['is_deleted'] . " to " . $newStatus);
            
            $result = $this->userModel->updateStatus($userId, $newStatus);
            
            if ($result) {
                $statusText = $newStatus ? 'locked' : 'unlocked';
                error_log("User " . $statusText . " successfully");
                $this->json(true, null, [
                    'message' => "User {$statusText} successfully",
                    'new_status' => $newStatus
                ]);
            } else {
                error_log("Failed to update user status");
                $this->json(false, 'Failed to update user status');
            }
        } catch (\Exception $e) {
            error_log("Error in toggleUserStatus: " . $e->getMessage());
            $this->json(false, 'Error: ' . $e->getMessage());
        }
    }

    private function deleteUser()
    {
        error_log("deleteUser called");
        
        try {
            $userId = $_POST['user_id'] ?? '';
            
            if (empty($userId)) {
                error_log("User ID is empty");
                $this->json(false, 'User ID is required');
                return;
            }
            
            error_log("Deleting user: " . $userId);
            
            $result = $this->userModel->softDelete($userId);
            
            if ($result) {
                error_log("User deleted successfully");
                $this->json(true, null, [
                    'message' => 'User deleted successfully'
                ]);
            } else {
                error_log("Failed to delete user");
                $this->json(false, 'Failed to delete user');
            }
        } catch (\Exception $e) {
            error_log("Error in deleteUser: " . $e->getMessage());
            $this->json(false, 'Error: ' . $e->getMessage());
        }
    }

    private function getPomodoroStats()
    {
        error_log("getPomodoroStats called");
        
        try {
            $days = (int)($_GET['days'] ?? 30);
            error_log("Getting pomodoro stats for " . $days . " days");
            
            $stats = $this->historyModel->getPomodoroStats($days);
            $topUsers = $this->historyModel->getTopUsersByStudyTime(10, $days);
            
            error_log("Stats: " . json_encode($stats));
            error_log("Top users count: " . count($topUsers));
            
            $this->json(true, null, [
                'stats' => $stats,
                'top_users' => $topUsers
            ]);
        } catch (\Exception $e) {
            error_log("Error in getPomodoroStats: " . $e->getMessage());
            $this->json(false, 'Error: ' . $e->getMessage());
        }
    }

    private function getUserActivity()
    {
        error_log("getUserActivity called");
        
        try {
            $days = (int)($_GET['days'] ?? 7);
            $page = (int)($_GET['page'] ?? 1);
            $limit = (int)($_GET['limit'] ?? 10);
            $userId = $_GET['user_id'] ?? null;
            
            error_log("Params - days: $days, page: $page, limit: $limit, user_id: " . ($userId ?? 'null'));
            
            if ($userId) {
                error_log("Getting activity for specific user: " . $userId);
                $activity = $this->historyModel->getUserActivityPaginated($userId, $page, $limit);
                $total = $this->historyModel->countUserActivity($userId, $days);
                $dailyActivity = $this->historyModel->getUserDailyActivity($userId, $days);
            } else {
                error_log("Getting global activity");
                $activity = $this->historyModel->getRecentActivityPaginated($page, $limit);
                $total = $this->historyModel->countRecentActivity($days);
                $dailyActivity = $this->historyModel->getGlobalDailyActivity($days);
            }
            
            error_log("Activity count: " . count($activity));
            error_log("Total activities: " . $total);
            error_log("Daily activity count: " . count($dailyActivity));
            
            $this->json(true, null, [
                'activity' => $activity,
                'daily_activity' => $dailyActivity,
                'pagination' => [
                    'page' => $page,
                    'limit' => $limit,
                    'total' => $total,
                    'total_pages' => ceil($total / $limit)
                ]
            ]);
        } catch (\Exception $e) {
            error_log("Error in getUserActivity: " . $e->getMessage());
            $this->json(false, 'Error: ' . $e->getMessage());
        }
    }

    private function getDailyActivity()
    {
        error_log("getDailyActivity called");
        
        try {
            $days = (int)($_GET['days'] ?? 7);
            
            error_log("Getting daily activity for " . $days . " days");
            
            $dailyActivity = $this->historyModel->getGlobalDailyActivity($days);
            
            error_log("Daily activity count: " . count($dailyActivity));
            
            $this->json(true, null, [
                'daily_activity' => $dailyActivity
            ]);
        } catch (\Exception $e) {
            error_log("Error in getDailyActivity: " . $e->getMessage());
            $this->json(false, 'Error: ' . $e->getMessage());
        }
    }

    private function getActivityTable()
    {
        error_log("getActivityTable called");
        
        try {
            $days = (int)($_GET['days'] ?? 7);
            $page = (int)($_GET['page'] ?? 1);
            $limit = (int)($_GET['limit'] ?? 10);
            
            error_log("Params - days: $days, page: $page, limit: $limit");
            
            $activity = $this->historyModel->getRecentActivityPaginated($page, $limit, $days);
            $total = $this->historyModel->countRecentActivity($days);
            
            error_log("Activity count: " . count($activity));
            error_log("Total activities: " . $total);
            
            $this->json(true, null, [
                'activity' => $activity,
                'pagination' => [
                    'page' => $page,
                    'limit' => $limit,
                    'total' => $total,
                    'total_pages' => ceil($total / $limit)
                ]
            ]);
        } catch (\Exception $e) {
            error_log("Error in getActivityTable: " . $e->getMessage());
            $this->json(false, 'Error: ' . $e->getMessage());
        }
    }
}
?>