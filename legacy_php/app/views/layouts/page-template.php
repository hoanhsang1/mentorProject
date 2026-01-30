<?php
/**
 * Page Template
 * Copy this file and rename for new pages
 */

// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit();
}

// Set page variables for layout
$page_title = 'Page Title'; // Thay đổi tiêu đề
$show_breadcrumb = true; // Hiển thị breadcrumb hay không

// Page content
ob_start();
?>
<!-- Your page content here -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Page Content</h2>
    </div>
    <div class="p-4">
        <p>This is your page content.</p>
        
        <div class="mt-4">
            <button class="btn btn-primary">Action Button</button>
            <button class="btn btn-secondary ml-2">Secondary</button>
        </div>
    </div>
</div>
<?php

$content = ob_get_clean();

// Include layout
require_once __DIR__ . '/includes/layout.php';