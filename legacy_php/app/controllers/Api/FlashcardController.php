<?php
namespace App\Controllers\Api;

use App\Core\Controller;
use App\Models\Flashcard\Flashcard;
use App\Models\Flashcard\FlashcardSet;
use App\Models\Flashcard\FlashcardItem;
use App\Models\Flashcard\Flashcardprogress;

class FlashcardController extends Controller
{
    private $action;
    private $userId;

    public function handle()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->json(false, 'Unauthorized');
            return;
        }

        $this->userId = $_SESSION['user_id'];
        $this->action = $_GET['action'] ?? $_POST['action'] ?? '';

        switch ($this->action) {
            case 'get_sets':
                $this->getSets();
                break;
            case 'create_set':
                $this->createSet();
                break;
            case 'update_set':
                $this->updateSet();
                break;
            case 'delete_set':
                $this->deleteSet();
                break;
            case 'get_cards':
                $this->getCards();
                break;
            case 'get_unlearned_cards':
                $this->getUnlearnedCards();
                break;
            case 'create_card':
                $this->createCard();
                break;
            case 'update_card':
                $this->updateCard();
                break;
            case 'delete_card':
                $this->deleteCard();
                break;
            case 'reset_progress':
                $this->resetSetProgress();
                break;
            case 'study_cards':
                $this->getStudyCards();
                break;
            case 'update_progress':
                $this->updateProgress();
                break;
            case 'get_stats':
                $this->getStats();
                break;
            default:
                $this->json(false, 'Invalid action');
        }
    }

    private function getSets()
    {
        $flashcardModel = new Flashcard();
        $userFlashcard = $flashcardModel->getUserFlashcard($this->userId);
        
        if (!$userFlashcard) {
            $this->json(false, 'Flashcard not found');
            return;
        }

        $setModel = new FlashcardSet();
        $sets = $setModel->getUserSets($userFlashcard['flashcard_id'], $this->userId);

        $this->json(true, null, ['sets' => $sets]);
    }

    private function createSet()
    {
        $title = trim($_POST['title'] ?? '');

        if (empty($title)) {
            $this->json(false, 'Title is required');
            return;
        }

        $flashcardModel = new Flashcard();
        $userFlashcard = $flashcardModel->getUserFlashcard($this->userId);
        
        if (!$userFlashcard) {
            // Tạo flashcard nếu chưa có
            $flashcardModel->createFlashcard($this->userId);
            $userFlashcard = $flashcardModel->getUserFlashcard($this->userId);
        }

        $setModel = new FlashcardSet();
        $setId = $setModel->createSet($userFlashcard['flashcard_id'], $title);

        if ($setId) {
            $this->json(true, null, [
                'set_id' => $setId,  // Dùng setId từ hàm createSet
                'title' => $title,
                'message' => 'Flashcard set created successfully'
            ]);
        } else {
            $this->json(false, 'Failed to create set');
        }
    }

    private function updateSet()
    {
        $setId = $_POST['set_id'] ?? '';
        $title = trim($_POST['title'] ?? '');

        if (empty($setId) || empty($title)) {
            $this->json(false, 'Set ID and title are required');
            return;
        }

        $setModel = new FlashcardSet();
        $result = $setModel->updateSet($setId, $title);

        if ($result) {
            $this->json(true, null, ['message' => 'Set updated successfully']);
        } else {
            $this->json(false, 'Failed to update set');
        }
    }

    private function deleteSet()
    {
        $setId = $_POST['set_id'] ?? '';

        if (empty($setId)) {
            $this->json(false, 'Set ID is required');
            return;
        }

        $setModel = new FlashcardSet();
        $result = $setModel->softDelete($setId);

        if ($result) {
            $this->json(true, null, ['message' => 'Set deleted successfully']);
        } else {
            $this->json(false, 'Failed to delete set');
        }
    }

    private function getCards()
    {
        $setId = $_GET['set_id'] ?? '';

        if (empty($setId)) {
            $this->json(false, 'Set ID is required');
            return;
        }

        $cardModel = new FlashcardItem();
        $cards = $cardModel->getSetCards($setId, $this->userId);

        $this->json(true, null, ['cards' => $cards]);
    }

    private function createCard()
    {
        $setId = $_POST['set_id'] ?? '';
        $question = trim($_POST['question'] ?? '');
        $answer = trim($_POST['answer'] ?? '');

        if (empty($setId) || empty($question) || empty($answer)) {
            $this->json(false, 'All fields are required');
            return;
        }

        $cardModel = new FlashcardItem();
        $cardId = $cardModel->createCard($setId, $question, $answer);

        if ($cardId) {
            $this->json(true, null, [
                'card_id' => $cardId,  // Dùng cardId từ hàm createCard
                'question' => $question,
                'answer' => $answer,
                'message' => 'Card created successfully'
            ]);
        } else {
            $this->json(false, 'Failed to create card');
        }
    }


    private function updateCard()
    {
        $cardId = $_POST['card_id'] ?? '';
        $question = trim($_POST['question'] ?? '');
        $answer = trim($_POST['answer'] ?? '');

        if (empty($cardId) || empty($question) || empty($answer)) {
            $this->json(false, 'All fields are required');
            return;
        }

        $cardModel = new FlashcardItem();
        $result = $cardModel->updateCard($cardId, $question, $answer);

        if ($result) {
            $this->json(true, null, ['message' => 'Card updated successfully']);
        } else {
            $this->json(false, 'Failed to update card');
        }
    }

    private function deleteCard()
    {
        $cardId = $_POST['card_id'] ?? '';

        if (empty($cardId)) {
            $this->json(false, 'Card ID is required');
            return;
        }

        $cardModel = new FlashcardItem();
        $result = $cardModel->softDelete($cardId);

        if ($result) {
            $this->json(true, null, ['message' => 'Card deleted successfully']);
        } else {
            $this->json(false, 'Failed to delete card');
        }
    }

    private function getStudyCards()
    {
        $setId = $_GET['set_id'] ?? '';
        
        if (!empty($setId)) {
            // Study specific set
            $cardModel = new FlashcardItem();
            $cards = $cardModel->getSetCards($setId, $this->userId);
        } else {
            // Study cards for review
            $progressModel = new Flashcardprogress();
            $cards = $progressModel->getReviewCards($this->userId, 20);
        }

        $this->json(true, null, ['cards' => $cards ?: []]);
    }

    private function updateProgress()
    {
        $cardId = $_POST['card_id'] ?? '';
        $status = $_POST['status'] ?? 'reviewed';

        if (empty($cardId)) {
            $this->json(false, 'Card ID is required');
            return;
        }

        $progressModel = new Flashcardprogress();
        $result = $progressModel->updateProgress($cardId, $this->userId, $status);

        if ($result) {
            $this->json(true, null, ['message' => 'Progress updated']);
        } else {
            $this->json(false, 'Failed to update progress');
        }
    }

    private function getStats()
    {
        $flashcardModel = new Flashcard();
        $userFlashcard = $flashcardModel->getUserFlashcard($this->userId);
        
        if (!$userFlashcard) {
            $this->json(false, 'Flashcard not found');
            return;
        }

        $setModel = new FlashcardSet();
        $sets = $setModel->getUserSets($userFlashcard['flashcard_id'], $this->userId);

        $totalCards = 0;
        $learnedCards = 0;
        $totalSets = count($sets);

        foreach ($sets as $set) {
            $totalCards += $set['total_cards'] ?? 0;
            $learnedCards += $set['learned_cards'] ?? 0;
        }

        $progress = $totalCards > 0 ? round(($learnedCards / $totalCards) * 100, 1) : 0;

        $this->json(true, null, [
            'total_sets' => $totalSets,
            'total_cards' => $totalCards,
            'learned_cards' => $learnedCards,
            'progress' => $progress,
            'sets' => $sets
        ]);
    }

    private function getUnlearnedCards()
    {
        $setId = $_GET['set_id'] ?? '';
        
        if (empty($setId)) {
            $this->json(false, 'Set ID is required');
            return;
        }
        
        $includeAll = $_GET['include_all'] ?? '0';
        
        $progressModel = new Flashcardprogress();
        
        if ($includeAll === '1') {
            // Học lại tất cả cards
            $cards = $progressModel->getAllCardsBySet($setId, $this->userId, 50);
        } else {
            // Chỉ học cards chưa thuộc
            $cards = $progressModel->getUnlearnedCardsBySet($setId, $this->userId, 50);
        }
        
        $this->json(true, null, [
            'cards' => $cards ?: [],
            'mode' => $includeAll === '1' ? 'all' : 'unlearned'
        ]);
    }

    // Thêm API reset progress
    private function resetSetProgress()
    {
        $setId = $_POST['set_id'] ?? '';
        
        if (empty($setId)) {
            $this->json(false, 'Set ID is required');
            return;
        }
        
        $progressModel = new Flashcardprogress();
        $result = $progressModel->resetSetProgress($setId, $this->userId);
        
        if ($result) {
            $this->json(true, null, [
                'message' => 'All cards have been reset to "unlearned" status'
            ]);
        } else {
            $this->json(false, 'Failed to reset progress');
        }
    }
}