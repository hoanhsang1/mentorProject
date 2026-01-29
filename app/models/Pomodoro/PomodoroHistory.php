<?php
namespace App\Models\Pomodoro;

use App\Core\Model;

class PomodoroHistory extends Model
{
    protected $table = 'pomodorohistory';
    protected $primaryKey = 'history_id';
    
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Tạo history record mới
     */
    public function createHistory($data)
    {
        try {
            $data['history_id'] = $this->generateUuid();
            return $this->create($data);
        } catch (\Exception $e) {
            error_log("PomodoroHistory create Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Lấy lịch sử pomodoro của user - SỬA: Chỉ lấy completed
     */
    public function getUserHistory($userId, $limit = 10)
    {
        try {
            $sql = "
                SELECT ph.*, p.title as pomodoro_title 
                FROM {$this->table} ph
                JOIN pomodoro p ON ph.pomodoro_id = p.pomodoro_id
                WHERE p.user_id = ? 
                    AND ph.is_deleted = 0
                    AND ph.status = 'completed'
                ORDER BY ph.start_time DESC
                LIMIT " . (int)$limit;  // CAST trong SQL
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId]);
            
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return is_array($result) ? $result : [];
            
        } catch (\Exception $e) {
            error_log("PomodoroHistory getUserHistory Error: " . $e->getMessage());
            return [];
        }
    }
        
    /**
     * Hoàn thành session
     */
    public function completeSession($historyId, $endTime, $durationMinutes)
    {
        $stmt = $this->db->prepare("
            UPDATE {$this->table} 
            SET end_time = ?, 
                duration_minutes = ?, 
                status = 'completed',
                updated_at = NOW()
            WHERE history_id = ?
        ");
        return $stmt->execute([$endTime, $durationMinutes, $historyId]);
    }
    
    /**
     * Lấy session đang active
     */
    public function getActiveSession($pomodoroId)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table} 
            WHERE pomodoro_id = ? AND status = 'in_progress' AND is_deleted = 0
            ORDER BY start_time DESC
            LIMIT 1
        ");
        $stmt->execute([$pomodoroId]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Thống kê pomodoro
     */
    public function getStats($userId, $dateFrom = null)
    {
        try {
            $sql = "
                SELECT 
                    COUNT(*) as total_sessions,
                    SUM(duration_minutes) as total_minutes,
                    AVG(duration_minutes) as avg_duration,
                    DATE(start_time) as session_date
                FROM {$this->table} ph
                JOIN pomodoro p ON ph.pomodoro_id = p.pomodoro_id
                WHERE p.user_id = ? 
                    AND ph.status = 'completed'  -- CHỈ lấy completed
                    AND ph.is_deleted = 0
            ";
            
            $params = [$userId];
            
            if ($dateFrom) {
                $sql .= " AND ph.start_time >= ?";
                $params[] = $dateFrom;
            }
            
            $sql .= " GROUP BY DATE(ph.start_time) ORDER BY session_date DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            return is_array($result) ? $result : [];
            
        } catch (\Exception $e) {
            error_log("PomodoroHistory getStats Error: " . $e->getMessage());
            return [];
        }
    }
        
    /**
     * Lấy thống kê hôm nay
     */
    public function getTodayStats($userId)
    {
        try {
            $today = date('Y-m-d');
            $sql = "
                SELECT 
                    COUNT(*) as today_sessions,
                    SUM(duration_minutes) as today_minutes
                FROM {$this->table} ph
                JOIN pomodoro p ON ph.pomodoro_id = p.pomodoro_id
                WHERE p.user_id = ? 
                    AND ph.status = 'completed' 
                    AND ph.is_deleted = 0
                    AND DATE(ph.start_time) = ?
            ";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId, $today]);
            return $stmt->fetch(\PDO::FETCH_ASSOC) ?: ['today_sessions' => 0, 'today_minutes' => 0];
            
        } catch (\Exception $e) {
            error_log("PomodoroHistory getTodayStats Error: " . $e->getMessage());
            return ['today_sessions' => 0, 'today_minutes' => 0];
        }
    }
    
    /**
     * Xóa mềm history
     */
    public function softDelete($historyId)
    {
        $stmt = $this->db->prepare("
            UPDATE {$this->table} 
            SET is_deleted = 1, updated_at = NOW() 
            WHERE history_id = ?
        ");
        return $stmt->execute([$historyId]);
    }
    
    /**
     * Lấy tổng thời gian học (phút) - MỚI THÊM
     */
    public function getTotalStudyTime()
    {
        $sql = "SELECT SUM(duration_minutes) as total_minutes 
                FROM {$this->table} 
                WHERE status = 'completed' AND is_deleted = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['total_minutes'] ?? 0;
    }
    
    /**
     * Lấy tổng số session - MỚI THÊM
     */
    public function getTotalSessions()
    {
        $sql = "SELECT COUNT(*) as total 
                FROM {$this->table} 
                WHERE status = 'completed' AND is_deleted = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }
    
    /**
     * Lấy thống kê theo ngày - MỚI THÊM
     */
    public function getDailyStats($days = 7)
    {
        $date = date('Y-m-d', strtotime("-$days days"));
        
        $sql = "SELECT 
                    DATE(start_time) as date,
                    COUNT(*) as sessions,
                    SUM(duration_minutes) as total_minutes,
                    AVG(duration_minutes) as avg_minutes
                FROM {$this->table} 
                WHERE start_time >= :date 
                    AND status = 'completed' 
                    AND is_deleted = 0
                GROUP BY DATE(start_time)
                ORDER BY date DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':date' => $date]);
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        return is_array($result) ? $result : [];
    }
    
    /**
     * Lấy thống kê pomodoro của user cụ thể - MỚI THÊM
     */
    public function getUserPomodoroStats($userId)
    {
        $sql = "SELECT 
                    COUNT(*) as total_sessions,
                    SUM(duration_minutes) as total_minutes,
                    AVG(duration_minutes) as avg_minutes,
                    MAX(start_time) as last_session
                FROM {$this->table} ph
                JOIN pomodoro p ON ph.pomodoro_id = p.pomodoro_id
                WHERE p.user_id = :user_id 
                    AND ph.status = 'completed' 
                    AND ph.is_deleted = 0";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: [
            'total_sessions' => 0,
            'total_minutes' => 0,
            'avg_minutes' => 0,
            'last_session' => null
        ];
    }
    
    /**
     * Lấy activity của user - MỚI THÊM
     */
    public function getUserActivity($userId, $limit = 10)
    {
        $sql = "SELECT ph.* 
                FROM {$this->table} ph
                JOIN pomodoro p ON ph.pomodoro_id = p.pomodoro_id
                WHERE p.user_id = :user_id 
                    AND ph.status = 'completed' 
                    AND ph.is_deleted = 0
                ORDER BY ph.start_time DESC
                LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }
    
    /**
     * Lấy thống kê pomodoro tổng quát - MỚI THÊM
     */
    public function getPomodoroStats($days = 30)
    {
        $date = date('Y-m-d', strtotime("-$days days"));
        
        $sql = "SELECT 
                    COUNT(*) as total_sessions,
                    SUM(duration_minutes) as total_minutes,
                    COUNT(DISTINCT p.user_id) as active_users,
                    AVG(duration_minutes) as avg_session_length
                FROM {$this->table} ph
                JOIN pomodoro p ON ph.pomodoro_id = p.pomodoro_id
                WHERE ph.start_time >= :date 
                    AND ph.status = 'completed' 
                    AND ph.is_deleted = 0";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':date' => $date]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: [
            'total_sessions' => 0,
            'total_minutes' => 0,
            'active_users' => 0,
            'avg_session_length' => 0
        ];
    }
    
    /**
     * Lấy top users theo thời gian học - MỚI THÊM
     */
    public function getTopUsersByStudyTime($limit = 10, $days = 30)
    {
        $date = date('Y-m-d', strtotime("-$days days"));
        
        $sql = "SELECT 
                    u.user_id,
                    u.username,
                    u.fullname,
                    COUNT(ph.history_id) as sessions,
                    SUM(ph.duration_minutes) as total_minutes
                FROM user u
                JOIN pomodoro p ON u.user_id = p.user_id
                JOIN {$this->table} ph ON p.pomodoro_id = ph.pomodoro_id
                WHERE ph.start_time >= :date 
                    AND ph.status = 'completed' 
                    AND ph.is_deleted = 0
                    AND u.is_deleted = 0
                GROUP BY u.user_id, u.username, u.fullname
                ORDER BY total_minutes DESC
                LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':date', $date);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }
    
    /**
     * Lấy recent activity - MỚI THÊM
     */
    public function getRecentActivity($limit = 50)
    {
        $sql = "SELECT 
                    ph.*,
                    u.username,
                    u.fullname
                FROM {$this->table} ph
                JOIN pomodoro p ON ph.pomodoro_id = p.pomodoro_id
                JOIN user u ON p.user_id = u.user_id
                WHERE ph.status = 'completed' 
                    AND ph.is_deleted = 0
                    AND u.is_deleted = 0
                ORDER BY ph.start_time DESC
                LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }
    
    /**
     * Lấy daily activity của user - MỚI THÊM
     */
    public function getUserDailyActivity($userId, $days = 7)
    {
        $date = date('Y-m-d', strtotime("-$days days"));
        
        $sql = "SELECT 
                    DATE(ph.start_time) as date,
                    COUNT(*) as sessions,
                    SUM(ph.duration_minutes) as total_minutes
                FROM {$this->table} ph
                JOIN pomodoro p ON ph.pomodoro_id = p.pomodoro_id
                WHERE p.user_id = :user_id 
                    AND ph.start_time >= :date 
                    AND ph.status = 'completed' 
                    AND ph.is_deleted = 0
                GROUP BY DATE(ph.start_time)
                ORDER BY date DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':user_id' => $userId,
            ':date' => $date
        ]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }
    
    /**
     * Lấy global daily activity - MỚI THÊM
     */
    public function getGlobalDailyActivity($days = 7)
    {
        $date = date('Y-m-d', strtotime("-$days days"));
        
        $sql = "SELECT 
                    DATE(ph.start_time) as date,
                    COUNT(*) as sessions,
                    SUM(ph.duration_minutes) as total_minutes,
                    COUNT(DISTINCT p.user_id) as active_users
                FROM {$this->table} ph
                JOIN pomodoro p ON ph.pomodoro_id = p.pomodoro_id
                WHERE ph.start_time >= :date 
                    AND ph.status = 'completed' 
                    AND ph.is_deleted = 0
                GROUP BY DATE(ph.start_time)
                ORDER BY date DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':date' => $date]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }
        
    /**
     * Lấy activity gần nhất (cho admin) - MỚI THÊM
     */
    public function getLatestActivity($limit = 20)
    {
        $sql = "SELECT 
                    ph.*,
                    u.username,
                    u.fullname,
                    p.title as pomodoro_title
                FROM {$this->table} ph
                JOIN pomodoro p ON ph.pomodoro_id = p.pomodoro_id
                JOIN user u ON p.user_id = u.user_id
                WHERE ph.status = 'completed' 
                    AND ph.is_deleted = 0
                    AND u.is_deleted = 0
                ORDER BY ph.start_time DESC
                LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }
    
    /**
     * Lấy thống kê theo tuần - MỚI THÊM
     */
    public function getWeeklyStats($weeks = 4)
    {
        $date = date('Y-m-d', strtotime("-$weeks weeks"));
        
        $sql = "SELECT 
                    YEARWEEK(start_time) as week,
                    COUNT(*) as sessions,
                    SUM(duration_minutes) as total_minutes,
                    COUNT(DISTINCT p.user_id) as active_users
                FROM {$this->table} ph
                JOIN pomodoro p ON ph.pomodoro_id = p.pomodoro_id
                WHERE ph.start_time >= :date 
                    AND ph.status = 'completed' 
                    AND ph.is_deleted = 0
                GROUP BY YEARWEEK(start_time)
                ORDER BY week DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':date' => $date]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Lấy recent activity với phân trang - MỚI THÊM
     */
    public function getRecentActivityPaginated($page = 1, $limit = 10, $days = 7)
    {
        $offset = ($page - 1) * $limit;
        $date = date('Y-m-d', strtotime("-$days days"));
        
        $sql = "SELECT 
                    ph.*,
                    u.username,
                    u.fullname,
                    p.title as pomodoro_title,
                    ph.study_topic
                FROM {$this->table} ph
                JOIN pomodoro p ON ph.pomodoro_id = p.pomodoro_id
                JOIN user u ON p.user_id = u.user_id
                WHERE ph.start_time >= :date 
                    AND ph.status = 'completed' 
                    AND ph.is_deleted = 0
                    AND u.is_deleted = 0
                ORDER BY ph.start_time DESC
                LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':date', $date);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Lấy user activity với phân trang - MỚI THÊM
     */
    public function getUserActivityPaginated($userId, $page = 1, $limit = 10)
    {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT ph.* 
                FROM {$this->table} ph
                JOIN pomodoro p ON ph.pomodoro_id = p.pomodoro_id
                WHERE p.user_id = :user_id 
                    AND ph.status = 'completed' 
                    AND ph.is_deleted = 0
                ORDER BY ph.start_time DESC
                LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Đếm total recent activity - MỚI THÊM
     */
    public function countRecentActivity($days = 7)
    {
        $date = date('Y-m-d', strtotime("-$days days"));
        
        $sql = "SELECT COUNT(*) as total
                FROM {$this->table} ph
                JOIN pomodoro p ON ph.pomodoro_id = p.pomodoro_id
                JOIN user u ON p.user_id = u.user_id
                WHERE ph.start_time >= :date 
                    AND ph.status = 'completed' 
                    AND ph.is_deleted = 0
                    AND u.is_deleted = 0";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':date' => $date]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    /**
     * Đếm user activity - MỚI THÊM
     */
    public function countUserActivity($userId, $days = 7)
    {
        $date = date('Y-m-d', strtotime("-$days days"));
        
        $sql = "SELECT COUNT(*) as total
                FROM {$this->table} ph
                JOIN pomodoro p ON ph.pomodoro_id = p.pomodoro_id
                WHERE p.user_id = :user_id 
                    AND ph.start_time >= :date 
                    AND ph.status = 'completed' 
                    AND ph.is_deleted = 0";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':user_id' => $userId,
            ':date' => $date
        ]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }
}