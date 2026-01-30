<?php
namespace App\Controllers\Web;

use App\Core\Controller;

class AdminController extends Controller
{
    public function index()
    {
        
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: /login');
            exit;
        }
        $page_css = ['/assets/css/modules/admin.css'];
        $page_js = ['/assets/js/modules/admin.js'];
        $admin_tab = 'dashboard'; // mặc định
        if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], '#') !== false) {
            $referer = $_SERVER['HTTP_REFERER'];
            $hash = parse_url($referer, PHP_URL_FRAGMENT);
            if ($hash && in_array($hash, ['dashboard', 'users', 'pomodoro', 'activity'])) {
                $admin_tab = $hash;
            }
        }
        $this->view('admin/index', [
            'page_title' => 'Admin Dashboard',
            'show_breadcrumb' => false,
            'page_css' => $page_css,
            'page_js' => $page_js,
            'sidebar_type' => 'admin',
            'current_page' => 'admin', // Quan trọng: để layout biết load admin-sidebar
            'admin_tab' => $admin_tab
        ]);
    }
}