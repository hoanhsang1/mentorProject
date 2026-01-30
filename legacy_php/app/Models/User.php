<?php
namespace App\Models;
require_once __DIR__ . '/../Core/Model.php';
use App\Core\Model;
use FFI\Exception;

class User extends Model
{
    protected $table = 'user';
    protected $primaryKey = 'user_id'; // Vì database của bạn dùng user_id
    
    /**
     * Find user by email - DÙNG query() method từ Base Model
     */
    public function findByEmail($email)
    {
        $result = $this->query(
            "SELECT * FROM {$this->table} WHERE email = ?", 
            [$email]
        );
        return $result->fetch(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Find user by username
     */
    public function findByUsername($username)
    {
        $result = $this->query(
            "SELECT * FROM {$this->table} WHERE username = ?", 
            [$username]
        );
        return $result->fetch(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Check if email exists
     */
    public function emailExists($email)
    {
        $user = $this->findByEmail($email);
        return $user !== false;
    }
    
    // Thêm vào class User trong User.php
    public function findById($user_id, $is_deleted=false)
    {
        try {
            if ($is_deleted) {
                $sql = "SELECT * FROM user WHERE user_id = :user_id LIMIT 1";
                
                } else {
                $sql = "SELECT * FROM user WHERE user_id = :user_id AND is_deleted = 0 LIMIT 1";

            }
            $stmt = $this->query($sql, ['user_id' => $user_id]);
            $user = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if ($user) {
                // Đảm bảo có 'id' key
                $user['user_id'] = $user['user_id'];
                return $user;
            }
            
            return false;
            
        } catch (Exception $e) {
            error_log("findById error: " . $e->getMessage());
            return false;
        }
    }

    public function getLastInsertedUser($username)
    {
        try {
            $sql = "SELECT * FROM user WHERE username = :username AND is_deleted = 0 ORDER BY created_at DESC LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['username' => $username]);
            $user = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if ($user) {
                // Đảm bảo có các key cần thiết
                if (isset($user['user_id']) && !isset($user['id'])) {
                    $user['id'] = $user['user_id'];
                }
                return $user;
            }
            
            return false;
            
        } catch (Exception $e) {
            error_log("getLastInsertedUser error: " . $e->getMessage());
            return false;
        }
    }
    /**
     * Create user with hashed password - DÙNG create() method từ Base Model
     */
    public function createUser($data)
    {
        try {
            error_log("=== CREATE USER DEBUG ===");
            error_log("Input data: " . print_r($data, true));
            
            // ✅ 1. TẠO UUID CHUẨN
            $data['user_id'] = $this->generateUuid();
            error_log("Generated UUID: " . $data['user_id']);
            
            // ✅ 2. Map field names - CHỈ map full_name → fullname
            if (isset($data['full_name'])) {
                $data['fullname'] = $data['full_name'];
                unset($data['full_name']);
            }
            // KHÔNG XÓA username và email!
            
            // ✅ 3. Hash password
            if (isset($data['password'])) {
                $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
                error_log("Password hashed");
            }
            
            // ✅ 4. Default values
            if (!isset($data['role'])) {
                $data['role'] = 'free';
            }
            if (!isset($data['is_deleted'])) {
                $data['is_deleted'] = 0;
            }
            
            // ✅ 5. Thêm timestamps
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['updated_at'] = date('Y-m-d H:i:s');
            
            error_log("Final data to insert: " . print_r($data, true));
            
            // ✅ 6. Insert vào database
            $result = $this->create($data);
            
            if ($result) {
                error_log("✅ User created successfully: " . $data['username']);
                
                // Trả về user data (bao gồm cả user_id)
                return $data;
            } else {
                error_log("❌ Failed to create user");
                return false;
            }
            
        } catch (Exception $e) {
            error_log("createUser error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update user password - DÙNG update() method từ Base Model
     */
    public function updatePassword($userId, $newPassword)
    {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        return $this->update($userId, [
            'password' => $hashedPassword,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Get all users with pagination
     */
    public function getAllPaginated($page = 1, $limit = 10)
    {
        $offset = ($page - 1) * $limit;
        
        $result = $this->query(
            "SELECT * FROM {$this->table} ORDER BY created_at DESC LIMIT ? OFFSET ?",
            [$limit, $offset]
        );
        
        return $result->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Search users by name or email
     */
    public function search($keyword)
    {
        $keyword = "%{$keyword}%";
        
        $result = $this->query(
            "SELECT * FROM {$this->table} WHERE username LIKE ? OR email LIKE ? OR fullname LIKE ?",
            [$keyword, $keyword, $keyword]
        );
        
        return $result->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Count total users
     */
    public function countAll()
    {
        $result = $this->query("SELECT COUNT(*) as total FROM {$this->table}");
        return $result->fetch(\PDO::FETCH_ASSOC)['total'];
    }
    
    /**
     * Get users by role
     */
    public function getByRole($role)
    {
        $result = $this->query(
            "SELECT * FROM {$this->table} WHERE role = ? ORDER BY created_at DESC",
            [$role]
        );
        
        return $result->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Soft delete user (update is_deleted flag) - DÙNG update() method
     */
    public function softDelete($userId)
    {
        return $this->update($userId, [
            'is_deleted' => 1,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Restore soft deleted user
     */
    public function restore($userId)
    {
        return $this->update($userId, [
            'is_deleted' => 0,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Get active users only (not deleted)
     */
    public function getActiveUsers()
    {
        $result = $this->query(
            "SELECT * FROM {$this->table} WHERE is_deleted = 0 ORDER BY created_at DESC"
        );
        
        return $result->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function authenticate($identifier, $password) {
        try {
            // SQL query tìm user bằng username HOẶC email
            $sql = "SELECT * FROM user 
                    WHERE (username = :identifier OR email = :identifier) 
                    AND is_deleted = 0 
                    LIMIT 1";
            
            $stmt = $this->query($sql, ['identifier' => $identifier]);
            $user = $stmt->fetch();
            
            // Kiểm tra nếu user tồn tại và password đúng
            if ($user && password_verify($password, $user['password'])) {
                // Xóa password trước khi trả về (bảo mật)
                unset($user['password']);
                return $user;
            }
            
            return false;
            
        } catch (Exception $e) {
            error_log("Authentication error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Lấy tổng số người dùng (mới thêm)
     */
    public function getTotalUsers()
    {
        $sql = "SELECT COUNT(*) as total FROM user WHERE is_deleted = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }
    
    /**
     * Lấy số người dùng mới trong X ngày (mới thêm)
     */
    public function getNewUsersLastDays($days)
    {
        $date = date('Y-m-d', strtotime("-$days days"));
        $sql = "SELECT COUNT(*) as count FROM user 
                WHERE created_at >= :date AND is_deleted = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':date' => $date]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    }
    
    /**
     * Lấy tất cả user với phân trang và tìm kiếm (mới thêm)
     */
    public function getAllUsers($page = 1, $limit = 10, $search = '', $isdelete=false)
    {
        $offset = ($page - 1) * $limit;
        if ($isdelete) {
            $sql = "SELECT * FROM user";

        } else {

            $sql = "SELECT * FROM user WHERE is_deleted = 0";
        }
        $params = [];
        
        if (!empty($search)) {
            $sql .= " AND (username LIKE :search OR email LIKE :search OR fullname LIKE :search)";
            $params[':search'] = "%$search%";
        }
        
        $sql .= " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Đếm tổng số user (có thể với tìm kiếm) (mới thêm)
     */
    public function countUsers($search = '')
    {
        $sql = "SELECT COUNT(*) as total FROM user WHERE is_deleted = 0";
        $params = [];
        
        if (!empty($search)) {
            $sql .= " AND (username LIKE :search OR email LIKE :search OR fullname LIKE :search)";
            $params[':search'] = "%$search%";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }
    
    /**
     * Reset password user (mới thêm)
     */
    public function resetPassword($userId, $hashedPassword)
    {
        $sql = "UPDATE user SET password = :password, updated_at = NOW() WHERE user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':password' => $hashedPassword,
            ':user_id' => $userId
        ]);
    }
    
    /**
     * Cập nhật trạng thái user (lock/unlock) (mới thêm)
     */
    public function updateStatus($userId, $status)
    {
        $sql = "UPDATE user SET is_deleted = :status, updated_at = NOW() WHERE user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':status' => $status,
            ':user_id' => $userId
        ]);
    }
    
    /**
     * Lấy thông tin user với thống kê pomodoro (mới thêm)
     */
    public function getUserWithStats($userId)
    {
        try {
            // Lấy thông tin user
            $user = $this->findById($userId);
            if (!$user) {
                return false;
            }
            
            // Lấy thống kê pomodoro
            $sql = "SELECT 
                        COUNT(ph.history_id) as total_sessions,
                        SUM(ph.duration_minutes) as total_minutes,
                        AVG(ph.duration_minutes) as avg_minutes,
                        MAX(ph.start_time) as last_session
                    FROM pomodorohistory ph
                    JOIN pomodoro p ON ph.pomodoro_id = p.pomodoro_id
                    WHERE p.user_id = :user_id 
                        AND ph.status = 'completed' 
                        AND ph.is_deleted = 0";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':user_id' => $userId]);
            $stats = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            return [
                'user' => $user,
                'stats' => $stats ?: [
                    'total_sessions' => 0,
                    'total_minutes' => 0,
                    'avg_minutes' => 0,
                    'last_session' => null
                ]
            ];
            
        } catch (\Exception $e) {
            error_log("getUserWithStats error: " . $e->getMessage());
            return false;
        }
    }
}
?>