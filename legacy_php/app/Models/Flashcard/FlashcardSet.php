<?php
namespace App\Models\Flashcard;

use App\Core\Model;

class FlashcardSet extends Model
{
    protected $table = 'flashcardset';
    protected $primaryKey = 'set_id';

    public function getUserSets($flashcardId, $userId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT fs.*, 
                       COUNT(DISTINCT fi.card_id) as total_cards,
                       COUNT(DISTINCT CASE WHEN fp.status = 'learned' THEN fp.card_id END) as learned_cards
                FROM flashcardset fs
                LEFT JOIN flashcarditem fi ON fs.set_id = fi.set_id AND fi.is_deleted = 0
                LEFT JOIN flashcardprogress fp ON fi.card_id = fp.card_id AND fp.user_id = ?
                WHERE fs.flashcard_id = ? AND fs.is_deleted = 0
                GROUP BY fs.set_id
                ORDER BY fs.created_at DESC
            ");
            $stmt->execute([$userId, $flashcardId]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log('Flashcardset getUserSets Error: ' . $e->getMessage());
            return [];
        }
    }

    public function createSet($flashcardId, $title)
    {
        try {
            $setId = $this->generateUuid(); // Tạo UUID trước
            
            $data = [
                'set_id' => $setId,
                'title' => $title,
                'flashcard_id' => $flashcardId,
                'created_at' => date('Y-m-d H:i:s')
            ];

            $result = $this->create($data);
            
            // Trả về setId nếu thành công
            return $result ? $setId : false;
            
        } catch (\PDOException $e) {
            error_log('Flashcardset createSet Error: ' . $e->getMessage());
            return false;
        }
    }

    public function updateSet($setId, $title)
    {
        try {
            return $this->update($setId, ['title' => $title]);
        } catch (\PDOException $e) {
            error_log('Flashcardset updateSet Error: ' . $e->getMessage());
            return false;
        }
    }
}