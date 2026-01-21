<?php

echo "<h2>Setup Upload System</h2>";
echo "<pre>";

// 1. Kiểm tra và tạo folder
$uploadDir = __DIR__ . '/assets/images/avatars/';

echo "1. Checking upload directory...\n";
if (!is_dir($uploadDir)) {
    if (mkdir($uploadDir, 0755, true)) {
        echo "   ✓ Created directory: $uploadDir\n";
    } else {
        echo "   ✗ Failed to create directory!\n";
    }
} else {
    echo "   ✓ Directory already exists: $uploadDir\n";
}

// 2. Kiểm tra quyền ghi
echo "\n2. Checking permissions...\n";
echo "   Directory writable: " . (is_writable($uploadDir) ? '✓ YES' : '✗ NO') . "\n";

// 3. Kiểm tra session
echo "\n3. Checking session...\n";
if (isset($_SESSION['user_id'])) {
    echo "   ✓ User ID in session: " . $_SESSION['user_id'] . "\n";
} else {
    echo "   ✗ No user ID in session\n";
}

// 4. Kiểm tra database connection
echo "\n4. Testing database connection...\n";
try {
    require_once __DIR__ . '/../../app/config/database.php';
    require_once __DIR__ . '/../../app/models/Users_avatar.php';
    
    $avatarModel = new App\Models\Users_avatar();
    echo "   ✓ Database connection successful\n";
    
    // Kiểm tra bảng
    $test = $avatarModel->selectAll();
    echo "   ✓ Table exists, records found: " . count($test) . "\n";
    
} catch (Exception $e) {
    echo "   ✗ Database error: " . $e->getMessage() . "\n";
}

echo "\n5. Folder structure:\n";
echo "   " . __DIR__ . "/\n";
echo "   ├── assets/\n";
echo "   │   └── images/\n";
echo "   │       └── avatars/ (upload directory)\n";
echo "   ├── upload.php\n";
echo "   └── profile.php\n";

echo "</pre>";
?>