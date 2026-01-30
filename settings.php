<?php
require 'includes/db.php';
require 'includes/auth.php';
require 'includes/i18n.php';
require 'includes/config.php';
require 'includes/security.php';

redirectIfNotLoggedIn();

$error = '';
$success = '';
$activeTab = $_GET['tab'] ?? 'profile';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $error = 'Invalid security token. Please try again.';
    } else {
        $action = $_POST['action'] ?? '';

        if ($action === 'update_profile') {
            try {
                $email = sanitizeInput($_POST['email'] ?? '');
                $password = $_POST['password'] ?? '';
                $avatar = $_FILES['avatar'] ?? null;

                // Validate email
                if (!isValidEmail($email)) {
                    throw new Exception(t('settings_email_invalid'));
                }

                // Check if email already exists
                $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
                $stmt->execute([$email, $_SESSION['user_id']]);
                if ($stmt->fetch()) {
                    throw new Exception(t('settings_email_taken'));
                }

                // Process avatar if provided
                if ($avatar && $avatar['tmp_name'] && $avatar['size'] > 0) {
                    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
                    if (!in_array($avatar['type'], $allowed_types)) {
                        throw new Exception(t('settings_invalid_image'));
                    }

                    if ($avatar['size'] > 2 * 1024 * 1024) {
                        throw new Exception('Avatar file size must not exceed 2MB');
                    }

                    // Create avatars directory if it doesn't exist
                    $avatarsDir = __DIR__ . '/uploads/avatars';
                    if (!is_dir($avatarsDir)) {
                        mkdir($avatarsDir, 0755, true);
                    }

                    $avatar_path = 'uploads/avatars/' . $_SESSION['user_id'] . '-' . time() . '.' . pathinfo($avatar['name'], PATHINFO_EXTENSION);
                    if (!move_uploaded_file($avatar['tmp_name'], __DIR__ . '/' . $avatar_path)) {
                        throw new Exception(t('settings_upload_failed'));
                    }

                    $stmt = $pdo->prepare("UPDATE users SET avatar = ? WHERE id = ?");
                    $stmt->execute([$avatar_path, $_SESSION['user_id']]);
                }

                // Update email
                $stmt = $pdo->prepare("UPDATE users SET email = ? WHERE id = ?");
                $stmt->execute([$email, $_SESSION['user_id']]);

                $success = t('settings_updated');
            } catch (Exception $e) {
                $error = $e->getMessage();
            }

        } elseif ($action === 'change_password') {
            try {
                $oldPassword = $_POST['old_password'] ?? '';
                $newPassword = $_POST['new_password'] ?? '';
                $confirmPassword = $_POST['confirm_password'] ?? '';

                if (empty($oldPassword)) {
                    throw new Exception('Current password is required');
                }

                if (empty($newPassword)) {
                    throw new Exception('New password is required');
                }

                if ($newPassword !== $confirmPassword) {
                    throw new Exception(t('register_password_mismatch'));
                }

                if (strlen($newPassword) < 8) {
                    throw new Exception('New password must be at least 8 characters');
                }

                // Verify old password
                $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
                $stmt->execute([$_SESSION['user_id']]);
                $user = $stmt->fetch();

                if (!password_verify($oldPassword, $user['password'])) {
                    throw new Exception('Current password is incorrect');
                }

                // Update password
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([$hashedPassword, $_SESSION['user_id']]);

                $success = 'Password changed successfully!';
                $activeTab = 'password';
            } catch (Exception $e) {
                $error = $e->getMessage();
                $activeTab = 'password';
            }
        }
    }
}

// Fetch user data
$stmt = $pdo->prepare("SELECT email, avatar FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

require 'templates/header.php';
?>
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="container mx-auto px-4 py-12">
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-800 mb-2"><?= t('settings_title') ?></h1>
            <p class="text-gray-600"><?= t('settings_subtitle') ?? 'Manage your account settings and preferences' ?></p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Sidebar Navigation -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">
                            <i class="fas fa-cog mr-2 text-blue-600"></i>Menu
                        </h3>
                        <nav class="space-y-2">
                            <a href="/settings?tab=profile" class="block px-4 py-3 rounded-lg transition <?= $activeTab === 'profile' ? 'bg-blue-100 text-blue-700 border-l-4 border-blue-600' : 'text-gray-700 hover:bg-gray-100' ?>">
                                <i class="fas fa-user mr-2"></i><?= t('settings_profile') ?? 'Profile' ?>
                            </a>
                            <a href="/settings?tab=password" class="block px-4 py-3 rounded-lg transition <?= $activeTab === 'password' ? 'bg-blue-100 text-blue-700 border-l-4 border-blue-600' : 'text-gray-700 hover:bg-gray-100' ?>">
                                <i class="fas fa-lock mr-2"></i><?= t('settings_password') ?? 'Password' ?>
                            </a>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="lg:col-span-3">
                <!-- Error Message -->
                <?php if ($error): ?>
                    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-600 rounded-lg">
                        <p class="text-red-700 flex items-center gap-2">
                            <i class="fas fa-exclamation-circle"></i>
                            <?= htmlspecialchars($error) ?>
                        </p>
                    </div>
                <?php endif; ?>

                <!-- Success Message -->
                <?php if ($success): ?>
                    <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-600 rounded-lg">
                        <p class="text-green-700 flex items-center gap-2">
                            <i class="fas fa-check-circle"></i>
                            <?= htmlspecialchars($success) ?>
                        </p>
                    </div>
                <?php endif; ?>

                <!-- Profile Tab -->
                <?php if ($activeTab === 'profile'): ?>
                    <div class="bg-white rounded-lg shadow-lg p-8">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6">
                            <i class="fas fa-user-circle mr-2 text-blue-600"></i><?= t('settings_profile') ?? 'Profile Settings' ?>
                        </h2>

                        <form method="POST" enctype="multipart/form-data" class="space-y-6">
                            <input type="hidden" name="action" value="update_profile">
                            <?= csrfTokenInput() ?>

                            <!-- Avatar Section -->
                            <div class="border-b border-gray-200 pb-6">
                                <label class="block text-sm font-semibold text-gray-700 mb-4">
                                    <i class="fas fa-image mr-2 text-blue-600"></i><?= t('settings_avatar_label') ?>
                                </label>
                                <div class="flex items-center gap-6">
                                    <div class="flex-shrink-0">
                                        <img 
                                            id="avatarPreview" 
                                            src="<?= $user['avatar'] ? htmlspecialchars($user['avatar']) : 'https://via.placeholder.com/100?text=Avatar' ?>" 
                                            alt="<?= t('settings_avatar_preview') ?>" 
                                            class="w-20 h-20 rounded-full border-4 border-blue-300 object-cover">
                                    </div>
                                    <div class="flex-1">
                                        <input 
                                            type="file" 
                                            name="avatar" 
                                            id="avatar" 
                                            accept="image/jpeg,image/png,image/gif" 
                                            class="block w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200 transition"
                                            onchange="previewAvatar(event)">
                                        <p class="text-gray-500 text-xs mt-2">PNG, JPG, GIF. Max 2MB</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Email Section -->
                            <div>
                                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-envelope mr-2 text-blue-600"></i><?= t('settings_email_label') ?>
                                </label>
                                <input 
                                    type="email" 
                                    name="email" 
                                    id="email" 
                                    value="<?= htmlspecialchars($user['email']) ?>" 
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200 transition"
                                    required>
                                <p class="text-gray-500 text-xs mt-1">Your email address for account recovery</p>
                            </div>

                            <!-- Submit Button -->
                            <div class="pt-4 border-t border-gray-200">
                                <button 
                                    type="submit" 
                                    class="bg-gradient-to-r from-blue-600 to-blue-800 text-white font-bold py-3 px-6 rounded-lg hover:from-blue-700 hover:to-blue-900 transition duration-300 flex items-center gap-2">
                                    <i class="fas fa-save"></i><?= t('settings_save_btn') ?>
                                </button>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>

                <!-- Password Tab -->
                <?php if ($activeTab === 'password'): ?>
                    <div class="bg-white rounded-lg shadow-lg p-8">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6">
                            <i class="fas fa-lock mr-2 text-blue-600"></i><?= t('settings_change_password') ?>
                        </h2>

                        <form method="POST" class="space-y-6">
                            <input type="hidden" name="action" value="change_password">
                            <?= csrfTokenInput() ?>

                            <!-- Current Password -->
                            <div>
                                <label for="old_password" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-key mr-2 text-blue-600"></i><?= t('settings_old_password') ?>
                                </label>
                                <input 
                                    type="password" 
                                    name="old_password" 
                                    id="old_password"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200 transition"
                                    required>
                                <p class="text-gray-500 text-xs mt-1">Enter your current password</p>
                            </div>

                            <!-- New Password -->
                            <div>
                                <label for="new_password" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-shield-alt mr-2 text-blue-600"></i><?= t('settings_new_password') ?>
                                </label>
                                <input 
                                    type="password" 
                                    name="new_password" 
                                    id="new_password"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200 transition"
                                    minlength="8"
                                    required>
                                <p class="text-gray-500 text-xs mt-1">Minimum 8 characters</p>
                            </div>

                            <!-- Confirm Password -->
                            <div>
                                <label for="confirm_password" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-check-circle mr-2 text-blue-600"></i><?= t('settings_confirm_password') ?>
                                </label>
                                <input 
                                    type="password" 
                                    name="confirm_password" 
                                    id="confirm_password"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200 transition"
                                    minlength="8"
                                    required>
                                <p class="text-gray-500 text-xs mt-1">Repeat your new password</p>
                            </div>

                            <!-- Password Requirements -->
                            <div class="bg-blue-50 border-l-4 border-blue-600 rounded-lg p-4">
                                <p class="text-blue-700 text-sm font-semibold mb-2">
                                    <i class="fas fa-info-circle mr-2"></i>Password Requirements:
                                </p>
                                <ul class="text-blue-600 text-xs space-y-1 ml-6">
                                    <li>• At least 8 characters long</li>
                                    <li>• Contains uppercase and lowercase letters</li>
                                    <li>• Contains numbers</li>
                                </ul>
                            </div>

                            <!-- Submit Button -->
                            <div class="pt-4 border-t border-gray-200">
                                <button 
                                    type="submit" 
                                    class="bg-gradient-to-r from-red-600 to-red-800 text-white font-bold py-3 px-6 rounded-lg hover:from-red-700 hover:to-red-900 transition duration-300 flex items-center gap-2">
                                    <i class="fas fa-save"></i>Update Password
                                </button>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    function previewAvatar(event) {
        const reader = new FileReader();
        reader.onload = function() {
            document.getElementById('avatarPreview').src = reader.result;
        };
        if (event.target.files[0]) {
            reader.readAsDataURL(event.target.files[0]);
        }
    }

    // Password confirmation validation
    const newPasswordInput = document.getElementById('new_password');
    const confirmPasswordInput = document.getElementById('confirm_password');

    if (newPasswordInput && confirmPasswordInput) {
        [newPasswordInput, confirmPasswordInput].forEach(input => {
            input.addEventListener('input', function() {
                if (confirmPasswordInput.value && newPasswordInput.value !== confirmPasswordInput.value) {
                    confirmPasswordInput.classList.add('border-red-500');
                } else {
                    confirmPasswordInput.classList.remove('border-red-500');
                }
            });
        });
    }
</script>

<?php require 'templates/footer.php'; ?>