<?php
namespace App\Controllers\Web;

use App\Core\Controller;
use App\Models\Users_avatar;

class ProfileController extends Controller
{
    public function index()
    {
        $avatarModel = new Users_avatar();
        $avatarPath = $avatarModel->getAvatar($_SESSION['user_id']) ?? null;

        $this->view('profile/index', [
            'page_title' => 'Profile',
            'avatarPath' => $avatarPath
        ]);
    }

    public function upload()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['avatar'])) {
            header("Location: /profile");
            exit;
        }

        $file = $_FILES['avatar'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = "Lỗi upload file!";
            header("Location: /profile");
            exit;
        }

        if ($file['size'] > 2000000) {
            $_SESSION['error'] = "File quá lớn!";
            header("Location: /profile");
            exit;
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg','jpeg','png','gif'])) {
            $_SESSION['error'] = "Sai định dạng!";
            header("Location: /profile");
            exit;
        }

        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/assets/images/avatars/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $newName = 'avatar_' . $_SESSION['user_id'] . '_' . time() . '.' . $ext;
        $fullPath = $uploadDir . $newName;

        if (!move_uploaded_file($file['tmp_name'], $fullPath)) {
            $_SESSION['error'] = "Không thể lưu file";
            header("Location: /profile");
            exit;
        }

        $pathForDb = 'assets/images/avatars/' . $newName;

        $model = new Users_avatar();
        $old = $model->getAvatar($_SESSION['user_id']);

        if ($old && file_exists($_SERVER['DOCUMENT_ROOT'] . '/' . $old)) {
            unlink($_SERVER['DOCUMENT_ROOT'] . '/' . $old);
        }

        $model->saveAvatar($_SESSION['user_id'], $pathForDb);
        $_SESSION['avatar_path'] = $pathForDb;
        $_SESSION['success'] = "Cập nhật avatar thành công";

        header("Location: /profile");
        exit;
    }
}
