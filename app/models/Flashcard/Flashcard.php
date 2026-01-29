<?php
namespace App\Models\Flashcard;

use App\Core\Model;

class Flashcard extends Model
{
    protected $table = 'flashcard';
    protected $primaryKey = 'flashcard_id';

    public function createFlashcard($userId)
    {
        $data = [
            'flashcard_id' => $this->generateUuid(),
            'user_id' => $userId,
            'created_at' => date('Y-m-d H:i:s')
        ];

        return $this->create($data);
    }

    public function getUserFlashcard($userId)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table} 
            WHERE user_id = ?
        ");
        $stmt->execute([$userId]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}