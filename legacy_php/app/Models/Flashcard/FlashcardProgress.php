<?php
namespace App\Models\Flashcard;

use App\Core\Model;

class Flashcardprogress extends Model
{
    protected $table = 'flashcardprogress';
    protected $primaryKey = 'progress_id';

    public function updateProgress($cardId, $userId, $status)
    {
        try {
            // Chỉ có 2 trạng thái: 'learned' (đã thuộc) hoặc 'new' (chưa thuộc)
            $validStatus = in_array($status, ['learned', 'new']) ? $status : 'new';
            
            // Kiểm tra xem đã có progress chưa
            $stmt = $this->db->prepare("
                SELECT * FROM {$this->table} 
                WHERE card_id = ? AND user_id = ?
            ");
            $stmt->execute([$cardId, $userId]);
            $existing = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($existing) {
                // Update existing
                $stmt = $this->db->prepare("
                    UPDATE {$this->table} 
                    SET status = ?, last_reviewed = NOW()
                    WHERE card_id = ? AND user_id = ?
                ");
                return $stmt->execute([$validStatus, $cardId, $userId]);
            } else {
                // Create new
                $progressId = $this->generateUuid();
                
                $data = [
                    'progress_id' => $progressId,
                    'card_id' => $cardId,
                    'user_id' => $userId,
                    'status' => $validStatus,
                    'last_reviewed' => date('Y-m-d H:i:s')
                ];
                return $this->create($data);
            }
        } catch (\PDOException $e) {
            error_log('Flashcardprogress updateProgress Error: ' . $e->getMessage());
            return false;
        }
    }

    public function getReviewCards($userId, $limit = 20)
    {
        try {
            $sql = "
                SELECT fi.*, fs.title as set_title
                FROM flashcarditem fi
                JOIN flashcardset fs ON fi.set_id = fs.set_id
                LEFT JOIN flashcardprogress fp ON fi.card_id = fp.card_id AND fp.user_id = :user_id
                WHERE fi.is_deleted = 0 
                AND fs.is_deleted = 0
                AND (fp.status IS NULL OR fp.status = 'new')
                ORDER BY COALESCE(fp.last_reviewed, '1970-01-01') ASC, 
                        fi.created_at ASC
                LIMIT :limit
            ";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':user_id', $userId, \PDO::PARAM_STR);
            $stmt->bindValue(':limit', (int)$limit, \PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
            
        } catch (\PDOException $e) {
            error_log('Flashcardprogress getReviewCards Error: ' . $e->getMessage());
            return [];
        }
    }

    public function getUnlearnedCardsBySet($setId, $userId, $limit = 50)
    {
        try {
            $sql = "
                SELECT fi.*
                FROM flashcarditem fi
                LEFT JOIN flashcardprogress fp ON fi.card_id = fp.card_id AND fp.user_id = :user_id
                WHERE fi.set_id = :set_id 
                AND fi.is_deleted = 0
                AND (fp.status IS NULL OR fp.status = 'new')
                ORDER BY COALESCE(fp.last_reviewed, '1970-01-01') ASC,
                        fi.created_at ASC
                LIMIT :limit
            ";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':user_id', $userId, \PDO::PARAM_STR);
            $stmt->bindValue(':set_id', $setId, \PDO::PARAM_STR);
            $stmt->bindValue(':limit', (int)$limit, \PDO::PARAM_INT);
            $stmt->execute();
            
            $cards = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
            
            // Nếu không có card chưa thuộc, trả về tất cả cards
            if (empty($cards)) {
                return $this->getAllCardsBySet($setId, $userId, $limit);
            }
            
            return $cards;
            
        } catch (\PDOException $e) {
            error_log('Flashcardprogress getUnlearnedCardsBySet Error: ' . $e->getMessage());
            return [];
        }
    }

    public function getAllCardsBySet($setId, $userId, $limit = 50)
    {
        try {
            $sql = "
                SELECT fi.*,
                    fp.status as progress_status,
                    fp.last_reviewed
                FROM flashcarditem fi
                LEFT JOIN flashcardprogress fp ON fi.card_id = fp.card_id AND fp.user_id = :user_id
                WHERE fi.set_id = :set_id 
                AND fi.is_deleted = 0
                ORDER BY 
                    CASE 
                        WHEN fp.status = 'new' OR fp.status IS NULL THEN 1
                        WHEN fp.status = 'learned' THEN 2
                        ELSE 3
                    END,
                    COALESCE(fp.last_reviewed, '1970-01-01') ASC,
                    fi.created_at ASC
                LIMIT :limit
            ";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':user_id', $userId, \PDO::PARAM_STR);
            $stmt->bindValue(':set_id', $setId, \PDO::PARAM_STR);
            $stmt->bindValue(':limit', (int)$limit, \PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
            
        } catch (\PDOException $e) {
            error_log('Flashcardprogress getAllCardsBySet Error: ' . $e->getMessage());
            return [];
        }
    }

    // Thêm hàm reset progress để học lại
    public function resetSetProgress($setId, $userId)
    {
        try {
            // Reset tất cả cards trong set về trạng thái 'new'
            $sql = "
                UPDATE {$this->table} fp
                JOIN flashcarditem fi ON fp.card_id = fi.card_id
                SET fp.status = 'new',
                    fp.last_reviewed = NULL
                WHERE fi.set_id = :set_id 
                AND fp.user_id = :user_id
            ";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':set_id' => $setId,
                ':user_id' => $userId
            ]);
            
        } catch (\PDOException $e) {
            error_log('Flashcardprogress resetSetProgress Error: ' . $e->getMessage());
            return false;
        }
    }
}