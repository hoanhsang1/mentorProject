<?php
namespace App\Controllers\Web;
use App\Core\Controller;
class CalendarController extends Controller{
    public function index() {
        $page_css = [
                        '/assets/css/modules/calendar.css',
                        '/assets/css/modules/fullcalendar.css'
                    ];
        $page_js = [
                        '/assets/js/modules/calendar.js',
                        '/assets/js/modules/fullcalendar.js'
                    ];
        $this->view('modules/calendar/index', [
            'page_title' => 'Calendar',
            'show_breadcrumb' => true,
            'currentMonth' => $_GET['month'] ?? date('m'),
            'currentYear' => $_GET['year'] ?? date('Y'),
            'selectedDate' => $_GET['date'] ?? date('Y-m-d'),
            'page_css' => $page_css,
            'page_js' => $page_js
        ]);
    }
}
