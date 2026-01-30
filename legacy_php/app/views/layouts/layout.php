<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$publicRoutes = ['/login', '/auth/login', '/auth/register'];

if (!isset($_SESSION['user_id']) && !in_array($currentPath, $publicRoutes)) {
    header('Location: /login');
    exit();
}

$user_id = $_SESSION['user_id'] ?? '';
$username = $_SESSION['username'] ?? 'User';
$fullname = $_SESSION['fullname'] ?? $username;
$role = $_SESSION['role'] ?? 'free';

$current_page = basename($_SERVER['PHP_SELF']);
?>


<!DOCTYPE html>
<html lang="vi" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyHub - <?php echo htmlspecialchars($page_title ?? 'Dashboard'); ?></title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- CSS -->
    <link rel="stylesheet" href="/assets/css/main.css">
    <link rel="stylesheet" href="/assets/css/layout.css">
    <link rel="stylesheet" href="/assets/css/components.css">
    <link rel="stylesheet" href="/assets/css/utilities.css">
    
    <!-- Favicon -->
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>📚</text></svg>">
    
    <?php if (isset($page_css)): ?>
        <!-- Page-specific CSS -->
        <?php foreach ($page_css as $css_file): ?>
            <link rel="stylesheet" href="<?php echo htmlspecialchars($css_file); ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <div class="layout-container">
        <!-- Header -->
        <?php include __DIR__ . '/header.php'; ?>
        
        <!-- Main Content -->
        <main class="main-content">
            <!-- Sidebar -->
            <?php if (isset($sidebar_type) && $sidebar_type === 'admin'): ?>
                <!-- Admin sidebar -->
                <?php include __DIR__ . '/admin-sidebar.php'; ?>
            <?php else: ?>
                <!-- Regular sidebar -->
                <?php include __DIR__ . '/sidebar.php'; ?>
            <?php endif; ?>
            
            <!-- Content Area -->
            <div class="content-area">
                <?php if (isset($show_breadcrumb) && $show_breadcrumb): ?>
                <div class="breadcrumb">
                    <a href="/dashboard.php" class="text-primary">Dashboard</a>
                    <span class="text-text-secondary">/</span>
                    <span class="text-text-secondary"><?php echo htmlspecialchars($page_title ?? 'Page'); ?></span>
                </div>
                <?php endif; ?>
                
                <h1 class="page-title"><?php echo htmlspecialchars($page_title ?? 'Dashboard'); ?></h1>
                
                <!-- Page content will be inserted here -->
                <?php echo $content ?? ''; ?>
            </div>
        </main>
    </div>
    
    <!-- JavaScript -->
    <script src="/assets/js/main.js"></script>
    <script src="/assets/js/layout.js"></script>
    
    <?php if (isset($page_js)): ?>
        <!-- Page-specific JS -->
        <?php foreach ($page_js as $js_file): ?>
            <script src="<?php echo htmlspecialchars($js_file); ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <?php if (isset($inline_js)): ?>
        <!-- Inline JavaScript -->
        <script>
            <?php echo $inline_js; ?>
        </script>
    <?php endif; ?>
    <div id="app-toast-container"></div>

</body>
</html>
