<?php
namespace App\Models;

require_once __DIR__ . '/../Core/Model.php';
use App\Core\Model;

class Users_avatar extends Model
{
    protected $table = 'users_avatar';
    protected $primaryKey = 'user_id';
    
    /**
     * Lưu avatar cho user
     */
    public function saveAvatar($userId, $avatarPath)
    {
        // Kiểm tra xem user đã có avatar chưa
        $existing = $this->findById($userId);
        
        if ($existing) {
            // Cập nhật avatar hiện có
            return $this->update($userId, ['path' => $avatarPath]);
        } else {
            // Tạo mới avatar
            return $this->create([
                'user_id' => $userId,
                'path' => $avatarPath
            ]);
        }
    }
    
    /**
     * Lấy avatar path theo user_id
     */
    public function getAvatar($userId)
    {
        $result = $this->findById($userId);
        return $result ? $result['path'] : null;
    }
    
    /**
     * Xóa avatar khỏi database
     */
    public function deleteAvatar($userId)
    {
        return $this->delete($userId);
    }
}