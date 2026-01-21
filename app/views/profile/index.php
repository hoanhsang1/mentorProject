<?php
// $avatarPath = $data['avatarPath'] ?? null;

?>

<!-- ==================== -->
<!--     PAGE CONTENT     -->
<!-- ==================== -->
<!-- Flash Messages -->
<?php if (isset($_SESSION['success'])): ?>
<div class="alert alert-success mb-6">
    <?php 
    echo htmlspecialchars($_SESSION['success']);
    unset($_SESSION['success']);
    ?>
</div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
<div class="alert alert-error mb-6">
    <?php 
    echo htmlspecialchars($_SESSION['error']);
    unset($_SESSION['error']);
    ?>
</div>
<?php endif; ?>

<style>
.profil {
    display: flex !important;
    flex-direction: row !important;
    justify-content: center;
    align-items: center !important;
}

.profile_avatar {
    border-radius: 50% !important;
    height: 150px;
    width: 150px;
    text-align: center;
    line-height: 150px;
    margin-right: 60px;
}

.upload_form {
    top: 0;
    left: 0;
    right: 0;
    z-index: 99;
    position: absolute;
    border-radius: 50% !important;
    height: 150px;
    width: 150px;
}

.hidden {
    display: none !important;
}

.avatar-container {
    position: relative;
    display: inline-block;
}

.avatar-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s;
    color: white;
    font-size: 14px;
}

.avatar-container:hover .avatar-overlay {
    opacity: 1;
}
</style>

<div class="space-y-6">
    <!-- Profile Header -->
    <div class="card">
        <div class="p-6">
            <div class="profil flex flex-col md:flex-row items-start md:items-center gap-6">
                <!-- Avatar -->
                <div class="avatar-container">
                    <div class="profile_avatar w-20 h-20 rounded-full bg-gradient-primary flex items-center justify-center text-white text-2xl font-bold shadow-md cursor-pointer relative overflow-hidden">
                        
                        <!-- Nếu có ảnh avatar thì hiển thị ảnh, không thì hiển thị chữ cái đầu -->
                        <?php if($avatarPath && file_exists($_SERVER['DOCUMENT_ROOT'] . '/' . $avatarPath)): ?>
                            <img src="<?php echo htmlspecialchars($avatarPath); ?>" 
                                alt="Avatar" 
                                class="w-full h-full rounded-full object-cover"
                                id="avatarImage">
                        <?php else: ?>
                            <span id="avatarInitial"><?php echo strtoupper(substr($_SESSION['fullname'] ?? $_SESSION['username'] ?? 'U', 0, 1)); ?></span>
                        <?php endif; ?>
                        
                        <!-- Form upload -->
                        <form id="avatarForm" action="/profile/upload" method="POST" enctype="multipart/form-data" class="upload_form">
                            <input type="file" 
                                name="avatar" 
                                id="avatarInput" 
                                accept="image/*"
                                class="hidden">
                        </form>
                        
                        <!-- Hover overlay -->
                        <div class="avatar-overlay">
                            <span>Change Avatar</span>
                        </div>
                    </div>
                    
                    <!-- Online status -->
                    <div class="absolute -bottom-2 -right-2 w-8 h-8 rounded-full bg-white border-2 border-white shadow-sm">
                        <div class="w-full h-full rounded-full bg-green-500 flex items-center justify-center">
                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                                <path d="M10 3L4.5 8.5L2 6" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                    </div>
                </div>
                
                <!-- Profile Info -->
                <div class="flex-1 min-w-0">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
                        <div class="min-w-0">
                            <h1 class="text-2xl font-bold text-text truncate"><?php echo htmlspecialchars($_SESSION['fullname'] ?? $_SESSION['username']); ?></h1>
                            <div class="flex items-center gap-2 text-text-secondary text-sm mt-1">
                                <span class="truncate">@<?php echo htmlspecialchars($_SESSION['username']); ?></span>
                                <span>•</span>
                                <span class="truncate"><?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?></span>
                            </div>
                        </div>
                        
                        <div class="flex flex-wrap gap-2 shrink-0">
                            <span class="badge badge-primary px-3 py-1"><?php echo htmlspecialchars(ucfirst($_SESSION['role'] ?? 'free')); ?> Plan</span>
                            <button class="btn btn-secondary px-4 py-2 text-sm">
                                <svg width="14" height="14" viewBox="0 0 16 16" fill="none" class="mr-2">
                                    <path d="M11.3333 2.00001C11.5084 1.82492 11.7163 1.68601 11.9452 1.59075C12.1741 1.4955 12.4197 1.44568 12.668 1.44401C12.9162 1.44234 13.1625 1.48884 13.3928 1.58091C13.6232 1.67298 13.8331 1.80886 14.0107 1.98093C14.1882 2.153 14.3299 2.35777 14.4275 2.58352C14.5251 2.80926 14.5765 3.05152 14.5787 3.29668C14.5809 3.54185 14.5339 3.78501 14.4403 4.01237C14.3467 4.23973 14.2083 4.44684 14.0333 4.62223L6.59999 12L2.66666 13.3333L3.99999 9.40001L11.3333 2.00001Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                Edit Profile
                            </button>
                        </div>
                    </div>
                    
                    <!-- Profile Meta -->
                    <div class="pt-4 border-t border-border">
                        <div class="flex flex-wrap gap-4 text-sm">
                            <div class="flex items-center gap-2 text-text-secondary">
                                <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
                                    <path d="M8 14C11.3137 14 14 11.3137 14 8C14 4.68629 11.3137 2 8 2C4.68629 2 2 4.68629 2 8C2 11.3137 4.68629 14 8 14Z" stroke="currentColor" stroke-width="1.5"/>
                                    <path d="M8 4V8L10 10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                </svg>
                                <span>Member since:</span>
                                <span class="font-medium text-text"><?php echo date('F j, Y', strtotime($_SESSION['created_at'] ?? 'now')); ?></span>
                            </div>
                            <div class="flex items-center gap-2 text-text-secondary">
                                <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
                                    <path d="M14 8C14 8 11.625 10.375 8 10.375C4.375 10.375 2 8 2 8C2 8 4.375 5.625 8 5.625C11.625 5.625 14 8 14 8Z" stroke="currentColor" stroke-width="1.5"/>
                                    <path d="M8 10C9.10457 10 10 9.10457 10 8C10 6.89543 9.10457 6 8 6C6.89543 6 6 6.89543 6 8C6 9.10457 6.89543 10 8 10Z" stroke="currentColor" stroke-width="1.5"/>
                                </svg>
                                <span>Last active:</span>
                                <span class="font-medium text-text">2 hours ago</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Personal Information -->
        <div class="lg:col-span-2">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Personal Information</h2>
                </div>
                <div class="space-y-6 p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="form-group">
                            <label class="form-label">Full Name</label>
                            <div class="form-control bg-bg-input border-border px-4 py-3 rounded-md">
                                <?php echo htmlspecialchars($_SESSION['fullname'] ?? 'Not set'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Username</label>
                            <div class="form-control bg-bg-input border-border px-4 py-3 rounded-md">
                                @<?php echo htmlspecialchars($_SESSION['username']); ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <div class="form-control bg-bg-input border-border px-4 py-3 rounded-md">
                            <?php echo htmlspecialchars($_SESSION['email'] ?? 'Not set'); ?>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="form-group">
                            <label class="form-label">Account Type</label>
                            <div class="form-control bg-bg-input border-border px-4 py-3 rounded-md">
                                <?php echo htmlspecialchars(ucfirst($_SESSION['role'] ?? 'free')); ?> Account
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Member Since</label>
                            <div class="form-control bg-bg-input border-border px-4 py-3 rounded-md">
                                <?php echo date('F j, Y', strtotime($_SESSION['created_at'] ?? 'now')); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Account Stats -->
        <div>
            <div class="card h-full">
                <div class="card-header">
                    <h2 class="card-title">Account Stats</h2>
                </div>
                <div class="space-y-4 p-6">
                    <div class="flex items-center justify-between p-3 rounded-lg bg-bg-input/50">
                        <div class="text-text-secondary text-sm">Total Study Time</div>
                        <div class="font-bold text-text">48h 30m</div>
                    </div>
                    <div class="flex items-center justify-between p-3 rounded-lg bg-bg-input/50">
                        <div class="text-text-secondary text-sm">Tasks Completed</div>
                        <div class="font-bold text-text">156</div>
                    </div>
                    <div class="flex items-center justify-between p-3 rounded-lg bg-bg-input/50">
                        <div class="text-text-secondary text-sm">Current Streak</div>
                        <div class="font-bold text-text">7 days</div>
                    </div>
                    <div class="flex items-center justify-between p-3 rounded-lg bg-bg-input/50">
                        <div class="text-text-secondary text-sm">Flashcards Mastered</div>
                        <div class="font-bold text-text">89</div>
                    </div>
                    <div class="flex items-center justify-between p-3 rounded-lg bg-bg-input/50">
                        <div class="text-text-secondary text-sm">Habits Tracked</div>
                        <div class="font-bold text-text">12</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const avatarContainer = document.querySelector('.avatar-container');
    const avatarInput = document.getElementById('avatarInput');
    const avatarForm = document.getElementById('avatarForm');
    
    // Click vào avatar để chọn file
    avatarContainer.addEventListener('click', function() {
        avatarInput.click();
    });
    
    // Khi chọn file
    avatarInput.addEventListener('change', function(e) {
        if (this.files && this.files[0]) {
            // Hiển thị preview
            const reader = new FileReader();
            reader.onload = function(e) {
                const avatarImage = document.getElementById('avatarImage');
                const avatarInitial = document.getElementById('avatarInitial');
                
                if (avatarImage) {
                    avatarImage.src = e.target.result;
                } else {
                    // Tạo img nếu chưa có
                    if (avatarInitial) avatarInitial.remove();
                    avatarContainer.querySelector('.profile_avatar').innerHTML = 
                        `<img src="${e.target.result}" alt="Preview" class="w-full h-full rounded-full object-cover" id="avatarImage">`;
                }
                
                // Hiển thị loading text
                const overlay = avatarContainer.querySelector('.avatar-overlay');
                overlay.innerHTML = '<span>Đang tải lên...</span>';
            };
            reader.readAsDataURL(this.files[0]);
            
            // Submit form
            avatarForm.submit();
        }
    });
});
</script>