<?php 
namespace App\Controllers\Web;

use App\Core\Controller;

class DashboardController extends Controller
{
    public function index() 
    {
        // $page_css = ['/assets/css/modules/calendar.css'];
        // $page_js = ['/assets/js/modules/todolist.js'];
        // 'page_css' => $page_css,
        //     'page_js' => $page_js
        $this->view('dashboard/index',[
            'page_title' => 'Dashboard',
            'show_breadcrumb' => true
        ]);
    }
}
