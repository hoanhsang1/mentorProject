// public/auth/js/login.js - PHIÊN BẢN CUỐI CÙNG
document.addEventListener('DOMContentLoaded', function() {
    // ========== ELEMENTS ==========
    const loginForm = document.getElementById('loginForm');
    if (!loginForm) return;
    
    const submitBtn = document.getElementById('submitBtn');
    const spinner = document.getElementById('loadingSpinner');
    const btnText = submitBtn ? submitBtn.querySelector('.btn-text') : null;
    const passwordInput = document.getElementById('password');
    const emailUsernameInput = document.getElementById('email_username');
    
    // ========== PASSWORD TOGGLE ==========
    const togglePassword = document.getElementById('togglePassword');
    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            const icon = this.querySelector('i');
            if (icon) {
                icon.className = type === 'password' ? 'fas fa-eye eye' : 'fas fa-eye-slash eye';
            }
        });
    }
    
    // ========== SIMPLE REMEMBER ME ==========
    if (emailUsernameInput) {
        // Lấy email từ localStorage nếu có
        const savedEmail = localStorage.getItem('studyhub_last_email');
        if (savedEmail) {
            emailUsernameInput.value = savedEmail;
        }
        
        // Auto-focus
        if (emailUsernameInput.value.trim()) {
            if (passwordInput) passwordInput.focus();
        } else {
            emailUsernameInput.focus();
        }
        
        // Lưu email khi user gõ
        emailUsernameInput.addEventListener('input', function() {
            const email = this.value.trim();
            if (email) {
                localStorage.setItem('studyhub_last_email', email);
            }
        });
        
        // Enter key navigation
        emailUsernameInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                if (passwordInput) passwordInput.focus();
            }
        });
    }
    
    if (passwordInput) {
        passwordInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                loginForm.dispatchEvent(new Event('submit'));
            }
        });
    }
    
    // ========== FORM SUBMIT - SIMPLE VALIDATION ==========
    let isSubmitting = false;
    
    loginForm.addEventListener('submit', function(e) {
        // Chỉ validate client-side, không AJAX
        
        if (isSubmitting) {
            e.preventDefault();
            return;
        }
        
        const emailUsername = document.getElementById('email_username');
        const password = document.getElementById('password');
        
        if (!emailUsername || !password) return;
        
        const emailUsernameValue = emailUsername.value.trim();
        const passwordValue = password.value;
        
        // Reset errors
        clearAllErrors();
        
        let isValid = true;
        
        // Simple validation
        if (!emailUsernameValue) {
            showFieldError('email_username', 'Vui lòng nhập email hoặc tên đăng nhập');
            isValid = false;
        }
        
        if (!passwordValue) {
            showFieldError('password', 'Vui lòng nhập mật khẩu');
            isValid = false;
        }
        
        if (!isValid) {
            e.preventDefault();
            shakeForm();
            return;
        }
        
        // Nếu validation passed, hiển thị loading
        isSubmitting = true;
        showLoading(true);
        
        // Lưu email
        if (emailUsernameValue) {
            localStorage.setItem('studyhub_last_email', emailUsernameValue);
        }
        
        // Form sẽ tự submit đến server
        // Không cần preventDefault()
    });
    
    // ========== DEMO ACCOUNT AUTO-FILL ==========
    const demoInfo = document.querySelector('.demo-info');
    if (demoInfo && demoInfo.querySelector('p')) {
        demoInfo.querySelector('p').style.cursor = 'pointer';
        demoInfo.querySelector('p').addEventListener('click', function() {
            if (emailUsernameInput) emailUsernameInput.value = 'admin';
            if (passwordInput) passwordInput.value = 'admin123';
            localStorage.setItem('studyhub_last_email', 'admin');
            showAlert('info', 'Đã điền thông tin admin. Nhấn "Đăng nhập" để thử nghiệm.');
        });
    }
    
    // ========== FORGOT PASSWORD ==========
    const forgotPasswordLink = document.querySelector('.forgot-password');
    if (forgotPasswordLink) {
        forgotPasswordLink.addEventListener('click', function(e) {
            e.preventDefault();
            showAlert('info', 'Tính năng đang phát triển.');
        });
    }
    
    // ========== HELPER FUNCTIONS ==========
    function showFieldError(fieldId, message) {
        const field = document.getElementById(fieldId);
        if (!field) return;
        
        field.classList.add('input-error');
        
        const formGroup = field.closest('.form-group');
        if (formGroup) {
            let errorElement = formGroup.querySelector('.error-message');
            if (!errorElement) {
                errorElement = document.createElement('div');
                errorElement.className = 'error-message';
                formGroup.appendChild(errorElement);
            }
            errorElement.textContent = message;
            errorElement.style.display = 'block';
        }
    }
    
    function clearAllErrors() {
        document.querySelectorAll('.error-message').forEach(el => {
            el.textContent = '';
            el.style.display = 'none';
        });
        document.querySelectorAll('.input-error').forEach(el => {
            el.classList.remove('input-error');
        });
    }
    
    function showLoading(isLoading) {
        if (isLoading) {
            if (btnText) btnText.style.display = 'none';
            if (spinner) spinner.classList.remove('hidden');
            if (submitBtn) submitBtn.disabled = true;
        } else {
            if (btnText) btnText.style.display = 'inline';
            if (spinner) spinner.classList.add('hidden');
            if (submitBtn) submitBtn.disabled = false;
        }
    }
    
    function shakeForm() {
        loginForm.classList.add('error-shake');
        setTimeout(() => loginForm.classList.remove('error-shake'), 500);
    }
    
    function showAlert(type, message) {
        const oldAlert = document.querySelector('.dynamic-alert');
        if (oldAlert) oldAlert.remove();
        
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} dynamic-alert`;
        
        const icons = {
            error: 'exclamation-circle',
            success: 'check-circle',
            info: 'info-circle'
        };
        
        alert.innerHTML = `
            <i class="fas fa-${icons[type] || 'info-circle'}"></i>
            <span>${message}</span>
        `;
        
        const authCard = document.querySelector('.auth-card');
        if (authCard) {
            const authHeader = authCard.querySelector('.auth-header');
            if (authHeader) {
                authCard.insertBefore(alert, authHeader.nextElementSibling);
            }
        }
        
        setTimeout(() => {
            if (alert.parentNode) alert.remove();
        }, 5000);
    }
    
    // ========== ADD CSS ANIMATIONS ==========
    const style = document.createElement('style');
    style.textContent = `
        .error-shake {
            animation: shake 0.5s ease;
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }
        .dynamic-alert {
            animation: slideDown 0.3s ease-out;
            padding: 12px 15px;
            border-radius: 8px;
            margin: 15px 0;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
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
        .alert-info {
            background-color: rgba(23, 162, 184, 0.1);
            color: #17a2b8;
            border-left: 4px solid #17a2b8;
        }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .input-error {
            border-color: #dc3545 !important;
            background-color: #fff8f8;
        }
        .error-message {
            color: #dc3545;
            font-size: 14px;
            margin-top: 5px;
            display: block;
        }
    `;
    document.head.appendChild(style);
});