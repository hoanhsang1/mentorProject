<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyHub | Đăng nhập</title>
    <link rel="stylesheet" href="/assets/css/modules/auth.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="/assets/images/favicon.ico">
    <style>
        .error-message {
            color: #dc3545;
            font-size: 14px;
            margin-top: 5px;
            display: block;
        }
        .alert {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideDown 0.3s ease-out;
        }
        .alert-error {
            background-color: rgba(220, 53, 69, 0.1);
            color: #dc3545;
            border-left: 4px solid #dc3545;
        }
        .alert-success {
            background-color: rgba(40, 167, 69, 0.1);
            color: #28a745;
            border-left: 4px solid #28a745;
        }
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .forgot-password {
            display: block;
            text-align: right;
            margin-bottom: 20px;
            color: #4a6cf7;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.2s;
        }
        .forgot-password:hover {
            color: #3a5bd9;
            text-decoration: underline;
        }
        .input-error {
            border-color: #dc3545 !important;
            background-color: #fff8f8;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <!-- Logo/Header -->
            <div class="auth-header">
                <div class="logo">
                    <i class="fas fa-graduation-cap"></i>
                    <span>StudyHub</span>
                </div>
                <h1 class="auth-title">Chào mừng trở lại</h1>
                <p class="auth-subtitle">Đăng nhập để tiếp tục học tập</p>
            </div>

            <!-- Success message from registration -->
            <?php if (isset($success_message)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <span><?php echo htmlspecialchars($success_message); ?></span>
            </div>
            <?php endif; ?>

            <!-- Error messages -->
            <?php if (isset($errors['general'])): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <span><?php echo htmlspecialchars($errors['general']); ?></span>
            </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form method="POST" action="/login" class="auth-form" id="loginForm">
                <!-- Email/Username Field -->
                <div class="form-group">
                    <label for="email_username">EMAIL HOẶC TÊN ĐĂNG NHẬP</label>
                    <div class="input-with-icon">
                        <i class="fas fa-user"></i>
                        <input type="text" 
                               id="email_username" 
                               name="email_username" 
                               value="<?php echo htmlspecialchars($email_username ?? ""); ?>"
                               placeholder="email@example.com hoặc tên đăng nhập"
                               class="<?php echo isset($errors['email_username']) ? 'input-error' : ''; ?>"
                               required
                               autofocus>
                    </div>
                    <?php if (isset($errors['email_username'])): ?>
                        <div class="error-message"><?php echo htmlspecialchars($errors['email_username'] ?? ""); ?></div>
                    <?php endif; ?>
                </div>

                <!-- Password Field -->
                <div class="form-group">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
                        <label for="password">MẬT KHẨU</label>
                        <a href="/forgot_password" class="forgot-password">Quên mật khẩu?</a>
                    </div>
                    <div class="input-with-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               placeholder="Nhập mật khẩu"
                               class="<?php echo isset($errors['password']) ? 'input-error' : ''; ?>"
                               required>
                        <button type="button" class="toggle-password" id="togglePassword">
                            <i class="fas fa-eye eye"></i>
                        </button>
                    </div>
                    <?php if (isset($errors['password'])): ?>
                        <div class="error-message"><?php echo htmlspecialchars($errors['password'] ?? ""); ?></div>
                    <?php endif; ?>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-center h-47 btn btn-primary btn-block" id="submitBtn">
                    <span class="btn-text">Đăng nhập</span>
                    <div class="btn-text spinner hidden h-47" id="loadingSpinner">
                        <div class="spinner-dot"></div>
                        <div class="spinner-dot"></div>
                        <div class="spinner-dot"></div>
                    </div>
                </button>

                
            </form>

            <!-- Register Link -->
            <div class="auth-footer">
                <p>
                    Chưa có tài khoản? 
                    <a href="/register" class="link">Đăng ký ngay</a>
                </p>
                <p class="copyright">
                    &copy; 2024 StudyHub. All rights reserved.
                </p>
            </div>
        </div>
    </div>

    <script src="/assets/js/modules/login.js"></script>

</body>
</html>