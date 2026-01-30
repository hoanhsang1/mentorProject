<?php
namespace App\Controllers\Web;
use App\Core\Controller;
use App\Models\Todo\Todolistgroup;
class TodoController extends Controller{
    public function index() {
        $modelGroup = new Todolistgroup();
        $page_css = ['/assets/css/modules/todo.css'];
        $page_js = ['/assets/js/modules/todolist.js'];
        $groups = $modelGroup->getAllGroupById($_SESSION['todolist'] ?? null);
        $this->view('modules/todo/index', [
            'page_title' => 'Todo List',
            'show_breadcrumb' => true,
            'groups' => $groups,
            'page_css' => $page_css,
            'page_js' => $page_js
        ]);
    }
}
