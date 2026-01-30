<?php
namespace App\Controllers\Web;

use App\Core\Controller;

class HabitController extends Controller
{
    public function index()
    {
        $page_css = ['/assets/css/modules/habit.css'];
        $page_js = ['/assets/js/modules/habit.js'];    
        $data = [
            'page_title' => 'Habit',
            'show_breadcrumb' => true,
            'page_css' => $page_css,
            'page_js' => $page_js
        ];
        $this->view('habit/index',$data);
    }
}