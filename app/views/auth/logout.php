<?php
// public/auth/logout.php
session_start();

// Xóa tất cả session variables
$_SESSION = [];

// Hủy session
session_destroy();

// Redirect về trang login
header('Location: /login?logout=success');
exit();
?>