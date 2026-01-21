<?php
namespace App\Controllers\Api;
require_once __DIR__ . '/../../Models/Todo/Todolistgroup.php';
require_once __DIR__ . '/../../Models/Todo/Todolist.php';
require_once __DIR__ . '/../../Models/Todo/Task.php';

use App\Models\Todo\Todolist;
use App\Models\Todo\Todolistgroup;
use App\Models\Todo\Task;

class TodoController
{

public function createGroup()
{
    $title = trim($_POST['title'] ?? '');
    $todolistId = $_SESSION['todolist'] ?? null;

    if ($title === '') {
        $this->json(false, "Tên group không được để trống"); return;
    }

    if (!$todolistId) {
        $this->json(false, "Thiếu todolist_id"); return;
    }

    $model = new Todolistgroup();
    $group = $model->createGroup($todolistId, $title);

    $this->json((bool)$group, null, ["group" => $group]);
}

public function updateGroup() {
    $title = trim($_POST['title'] ?? "");
    $id = $_POST['groupId'] ?? "";

    if ($id === "") {
        $this->json(false, "Thiếu gropu id"); return;
    }

    if ($title === '') {
        $this->json(false, "Tên group không được để trống");
    }

    $model = new Todolistgroup();
    $group = $model->update($id,['title'=> $title]);
    $this->json((bool)$group,null);
    // $this->json(true, null, ["group" => $group]);
}

public function deleteGroup() {
    $id = $_POST['groupId'] ?? "";

    if ($id === "") {
        $this->json(false, "Thiếu gropu id"); return;
    }

    

    $model = new Todolistgroup();
    $group = $model->softDelete($id);
    $this->json((bool)$group,null);
}

public function getAllTask() {
    $id = $_GET['groupId'] ?? "";

    if ($id === "") {
        $this->json(false, "Thiếu gropu id"); return;
    }

    $model = new Task();
    $model->markOverdueByGroup($id);   // update overdue một lần
    $tasks = $model->getAllTaskByGroupId($id);
     if ($tasks === false) {
        $this->json(false, "Error loading tasks");
        return;
    }
    $this->json(true,null,["tasks" => $tasks]);
}

public function createTask() {
    header('Content-Type: application/json');
    
    if (session_status() === PHP_SESSION_NONE) session_start();
    
    if (!isset($_SESSION['user_id'])) {
        $this->json(false, "Unauthorized"); 
        return;
    }
    
    // Nhận dữ liệu
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $priority = $_POST['priority'] ?? 'medium';
    $status = $_POST['status'] ?? 'pending';
    $dueDate = $_POST['deadline'] ?? null;
    $groupId = $_POST['group_id'] ?? null;
    
    // Validate
    if (empty($title)) {
        $this->json(false, "Task name is required");
        return;
    }
    
    if (empty($groupId)) {
        $this->json(false, "Group ID is required");
        return;
    }
    
    // Chuẩn bị data
    $taskData = [
        'task' => $title,
        'description' => $description,
        'priority' => $priority,
        'status' => $status,
        'group_id' => $groupId
    ];
    
    if (!empty($dueDate)) {
        $taskData['deadline'] = $dueDate;
    }
    
    // Tạo task
    $model = new Task();
    $task = $model->createTask($taskData); // Gọi createTask() thay vì create()
    
    if ($task) {
        $this->json(true, null, ["task" => $task]);
    } else {
        $this->json(false, "Failed to create task");
    }
}

public function toggleStatus() {
    $id = $_POST['id'] ?? null;
    if (!$id) {
        $this->json(false, "Missing id");
        return;
    }

    $model = new Task();
    $task = $model->toggleStatus($id);
    
    if (!$task) {
        $this->json(false, "Update failed");
        return;
    }

    $this->json(true, null, ["task" => $task]);
}


public function deleteTask() {
    header('Content-Type: application/json');

    $id = $_POST['id'] ?? null;
    if (!$id) {
        $this->json(false, "Missing id");
        return;
    }

    $model = new Task();
    $ok = $model->softDelete($id);

    if ($ok) {
        $this->json(true, null);
    } else {
        $this->json(false, "Delete failed");
    }
}

public function getTaskById()
{
    $id = $_GET['taskId'] ?? null;

    if (!$id) {
        $this->json(false, "Missing taskId");
        return;
    }

    $model = new Task();
    $task = $model->getTaskByTaskId($id);

    if (!$task) {
        $this->json(false, "Task not found");
        return;
    }

    $this->json(true, null, ["task" => $task]);
}

public function getTaskDetail() {
    $id = $_GET['id'] ?? null;

    if (!$id) {
        $this->json(false, "Missing task id");
        return;
    }

    $model = new Task();
    $task = $model->getTaskByTaskId($id);

    if (!$task) {
        $this->json(false, "Task not found");
        return;
    }

    $this->json(true, null, ["task" => $task]);
}


public function updateTask()
{
    $id = $_POST['id'] ?? null;
    if (!$id) {
        $this->json(false, "Missing id");
        return;
    }

    $data = [
        'title'       => $_POST['title'] ?? '',
        'description' => $_POST['description'] ?? '',
        'priority'    => $_POST['priority'] ?? 'medium',
        'deadline'    => $_POST['deadline'] ?? null,
        'status'      => $_POST['status'] ?? 'pending',
        'group_id'    => $_POST['group_id'] ?? null
    ];

    $model = new Task();
    $model->update($id, $data);

    $task = $model->getTaskByTaskId($id);

    $this->json(true, null, ["task" => $task]);
}


private function json($success, $error = null, $extra = [])
{
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(array_merge([
        "success" => $success,
        "error" => $error
    ], $extra));
    exit;
}



}
