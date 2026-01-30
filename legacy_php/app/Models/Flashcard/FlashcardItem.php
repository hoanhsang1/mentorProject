<?php
namespace App\Models\Flashcard;

use App\Core\Model;

class FlashcardItem extends Model
{
    protected $table = 'flashcarditem';
    protected $primaryKey = 'card_id';

    public function getSetCards($setId, $userId)
    {   if (empty($setId)) {
            return []; // Trả về mảng rỗng nếu setId rỗng
        }
        $stmt = $this->db->prepare("
            SELECT fi.*, 
                   fp.status as progress_status,
                   fp.last_reviewed,
                   fp.progress_id
            FROM flashcarditem fi
            LEFT JOIN flashcardprogress fp ON fi.card_id = fp.card_id AND fp.user_id = ?
            WHERE fi.set_id = ? AND fi.is_deleted = 0
            ORDER BY fi.created_at DESC
        ");
        $stmt->execute([$userId, $setId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function createCard($setId, $question, $answer)
    {
        try {
            $cardId = $this->generateUuid(); // Tạo UUID trước
            
            $data = [
                'card_id' => $cardId,
                'set_id' => $setId,
                'question' => $question,
                'answer' => $answer,
                'learned' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ];

            $result = $this->create($data);
            
            // Trả về cardId nếu thành công
            return $result ? $cardId : false;
            
        } catch (\PDOException $e) {
            error_log('Flashcarditem createCard Error: ' . $e->getMessage());
            return false;
        }
    }

    public function updateCard($cardId, $question, $answer)
    {
        return $this->update($cardId, [
            'question' => $question,
            'answer' => $answer,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }
}