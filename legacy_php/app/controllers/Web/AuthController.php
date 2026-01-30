<?php 
namespace App\Controllers\Web;

use App\Core\Controller;
use App\Models\User;
use App\Models\Users_avatar;
use App\Models\Todo\Todolist;

class AuthController extends Controller
{
    public function loginForm()
    {
        $this->view('auth/login', [
            'email_username' => '',
            'errors' => []
        ], false);
    }

    public function login()
    {
        $errors = [];
        $email_username = trim($_POST['email_username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!$email_username) {
            $errors['email_username'] = 'Vui lòng nhập email hoặc tên đăng nhập';
        }

        if (!$password) {
            $errors['password'] = 'Vui lòng nhập mật khẩu';
        }

        if (!empty($errors)) {
            return $this->view('auth/login', compact('errors', 'email_username'), false);
            // return $this->loginForm()
        }

        $userModel = new User();
        $userAvatar = new Users_avatar();
        $todolist = new Todolist();

        $user = $userModel->authenticate($email_username, $password);

        if (!$user) {
            $errors['general'] = 'Sai thông tin đăng nhập';
            return $this->view('auth/login', compact('errors', 'email_username'), false);
        }

        $_SESSION['user_id'] = $user['user_id'] ?? $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['fullname'] = $user['fullname'] ?? 'User';
        $_SESSION['role'] = $user['role'] ?? 'free';

        $_SESSION['avatar_path'] = $userAvatar->getAvatar($_SESSION['user_id']);
        if ($user['role']==="admin") {
            header('Location: /admin');
            exit;
        }
        if (!$todolist->findTodolistByUser($_SESSION['user_id'])) {
            $todolist->createTodolist($_SESSION['user_id']);
        }

        $_SESSION['todolist'] = $todolist->findTodolistByUser($_SESSION['user_id'])['todolist_id'];

        header('Location: /dashboard');
        exit;
    }

    public function logout()
    {
        session_destroy();
        header('Location: /login');
        exit;
    }

    public function registerForm()
    {
        $this->view('auth/register', [
            'username' => '',
            'fullname' => '',
            'email' => ''
        ], false);
    }

    public function register()
    {
        $username = trim($_POST['username'] ?? '');
        $fullname = trim($_POST['fullname'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';
        $agreeTerms = isset($_POST['agree_terms']);

        $errors = [];

        // Kiểm tra dữ liệu
        if (!$username) $errors['username'] = 'Thiếu username';
        if (!$email) $errors['email'] = 'Thiếu email';
        if (!$agreeTerms) $errors['terms'] = 'Bạn cần đồng ý với điều khoản sử dụng';
        if ($password !== $confirm) $errors['confirm_password'] = 'Mật khẩu không khớp';

        // Kiểm tra password
        if (strlen($password) < 6) {
            $errors['password'] = 'Mật khẩu phải có ít nhất 6 ký tự';
        }

        // Kiểm tra trùng username và email
        $user = new \App\Models\User();
        if ($username && $user->findByUsername($username)) {
            $errors['username'] = 'Tên đăng nhập đã được sử dụng';
        }
        if ($email && $user->findByEmail($email)) {
            $errors['email'] = 'Email đã được sử dụng';
        }

        // Nếu có lỗi, hiển thị form với thông báo lỗi
        if ($errors) {
            return $this->view('auth/register', compact('errors','username','fullname','email'), false);
        }

        // Tạo user
        $created = $user->createUser([
            'username' => $username,
            'fullname' => $fullname,
            'email' => $email,
            'password' => $password,
        ]);

        if (!$created) {
            $errors['general'] = 'Không tạo được tài khoản';
            return $this->view('auth/register', compact('errors'), false);
        }

        header('Location: /login');
        exit;
    }

}
