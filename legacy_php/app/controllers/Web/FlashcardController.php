<?php
namespace App\Controllers\Web;

use App\Core\Controller;
use App\Models\Flashcard\Flashcard;
use App\Models\Flashcard\FlashcardSet;

class FlashcardController extends Controller
{
    public function index()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /study-tools-website/login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        
        // Lấy hoặc tạo flashcard chính cho user
        $flashcardModel = new Flashcard();
        $userFlashcard = $flashcardModel->getUserFlashcard($userId);
        
        if (!$userFlashcard) {
            $flashcardModel->createFlashcard($userId);
            $userFlashcard = $flashcardModel->getUserFlashcard($userId);
        }

        $setModel = new FlashcardSet();
        $sets = $setModel->getUserSets($userFlashcard['flashcard_id'], $userId);
        $page_css = ['/assets/css/modules/flashcards.css'];
        $page_js = ['/assets/js/modules/flashcards.js'];    

        $data = [
            'page_title' => 'Flashcards',
            'sets' => $sets,
            'total_sets' => count($sets),
            'page_css' => $page_css,
            'show_breadcrumb' => true,
            'page_js' => $page_js

        ];

        $this->view('modules/flashcards/index', $data);
    }
}