<?php
namespace App\Controllers\Web;

use App\Core\Controller;

class PomodoroController extends Controller
{
    public function index()
    {   
        $page_css = ['/assets/css/modules/pomodoro.css'];
        $page_js = ['/assets/js/modules/pomodoro.js'];
        
        $this->view('modules/pomodoro/index',[
            'page_title' => 'Pomodoro',
            'show_breadcrumb' => true,
            'page_css' => $page_css,
            'page_js' => $page_js
        ]);
    }
}