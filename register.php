<?php
require 'includes/db.php';
require 'includes/auth.php';
require 'includes/i18n.php';
require 'includes/config.php';
require 'includes/security.php';
require 'includes/email.php';

if (isLoggedIn()) {
    header('Location: /dashboard');
    exit();
}

$error = '';
$success = '';
$isRateLimited = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $error = 'Invalid security token. Please try again.';
    } else {
        // Check rate limit for registration
        if (!checkRateLimit('register_' . $_SERVER['REMOTE_ADDR'], 3, 3600)) {
            $isRateLimited = true;
            $error = 'Too many registration attempts from your IP. Please try again later.';
        } else {
            $username = sanitizeInput($_POST['username'] ?? '');
            $email = sanitizeInput($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            // Validation
            if (strlen($username) < 3) {
                $error = t('register_username_min');
            } elseif (strlen($username) > 30) {
                $error = 'Username must not exceed 30 characters';
            } elseif (!isValidEmail($email)) {
                $error = t('register_email_invalid');
            } elseif (strlen($password) < 8) {
                $error = 'Password must be at least 8 characters';
            } elseif ($password !== $confirmPassword) {
                $error = t('register_password_mismatch');
            } else {
                try {
                    // Check if username/email already exists
                    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
                    $stmt->execute([$username, $email]);
                    $existingUser = $stmt->fetch();

                    if ($existingUser) {
                        // Check which one exists
                        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
                        $stmt->execute([$username]);
                        if ($stmt->fetch()) {
                            $error = t('register_username_taken');
                        } else {
                            $error = t('register_email_taken');
                        }
                    } else {
                        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                        $verificationToken = bin2hex(random_bytes(32));
                        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role, email_verification_token) VALUES (?, ?, ?, 'user', ?)");
                        $stmt->execute([$username, $email, $passwordHash, $verificationToken]);
                        
                        // Send verification email
                        if (sendVerificationEmail($email, $verificationToken)) {
                            $success = t('email_verification_sent');
                        } else {
                            $success = t('register_success') . ' ' . t('email_verification_sent');
                        }
                        header('refresh:3; url=login');
                    }
                } catch (PDOException $e) {
                    $error = t('register_error_default');
                }
            }
        }
    }
}

require 'templates/header.php';
?>
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center gap-2 mb-4">
                <div class="text-5xl text-blue-600"><i class="fas fa-smile"></i></div>
                <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent"><?= getSiteName() ?></h1>
            </div>
            <p class="text-gray-600"><?= t('register_title') ?> <?= t('register_and_start') ?></p>
        </div>

        <?php if ($error): ?>
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-600 rounded-lg">
                <p class="text-red-700 flex items-center gap-2">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= htmlspecialchars($error) ?>
                </p>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-600 rounded-lg">
                <p class="text-green-700 flex items-center gap-2">
                    <i class="fas fa-check-circle"></i>
                    <?= htmlspecialchars($success) ?>
                </p>
            </div>
        <?php endif; ?>

        <?php if ($isRateLimited): ?>
            <div class="mb-6 p-4 bg-orange-50 border-l-4 border-orange-600 rounded-lg">
                <p class="text-orange-700 text-sm flex items-center gap-2">
                    <i class="fas fa-clock"></i>
                    <?= t('too_many_attempts') ?>
                </p>
            </div>
        <?php endif; ?>
        
        <form method="POST" class="bg-white shadow-2xl rounded-xl p-8 space-y-6">
            <?= csrfTokenInput() ?>
            
            <div>
                <label for="username" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-user mr-2 text-blue-600"></i><?= t('register_username') ?>
                </label>
                <input 
                    type="text" 
                    name="username" 
                    id="username"
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200 transition" 
                    placeholder="<?= t('register_username') ?>"
                    minlength="3"
                    maxlength="30"
                    required
                    <?php if ($isRateLimited): ?>disabled<?php endif; ?>
                >
                <p class="text-gray-500 text-xs mt-1"><?= t('register_min_username_hint') ?></p>
            </div>

            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-envelope mr-2 text-blue-600"></i><?= t('register_email') ?>
                </label>
                <input 
                    type="email" 
                    name="email" 
                    id="email"
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200 transition" 
                    placeholder="email@example.com"
                    required
                    <?php if ($isRateLimited): ?>disabled<?php endif; ?>
                >
            </div>

            <div>
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-lock mr-2 text-blue-600"></i><?= t('register_password') ?>
                </label>
                <input 
                    type="password" 
                    name="password" 
                    id="password"
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200 transition" 
                    placeholder="<?= t('register_password') ?>"
                    minlength="8"
                    required
                    <?php if ($isRateLimited): ?>disabled<?php endif; ?>
                >
                <p class="text-gray-500 text-xs mt-1"><?= t('register_min_password_hint') ?></p>
            </div>

            <div>
                <label for="confirm_password" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-lock mr-2 text-blue-600"></i><?= t('register_confirm_password') ?>
                </label>
                <input 
                    type="password" 
                    name="confirm_password" 
                    id="confirm_password"
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200 transition" 
                    placeholder="<?= t('register_confirm_password') ?>"
                    minlength="8"
                    required
                    <?php if ($isRateLimited): ?>disabled<?php endif; ?>
                >
            </div>

            <div>
                <button 
                    type="submit" 
                    class="w-full bg-gradient-to-r from-blue-600 to-blue-800 text-white font-bold py-3 px-4 rounded-lg hover:from-blue-700 hover:to-blue-900 transition duration-300 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100"
                    <?php if ($isRateLimited): ?>disabled<?php endif; ?>>
                    <i class="fas fa-user-plus mr-2"></i><?= t('register_submit') ?>
                </button>
            </div>

            <div class="flex items-center my-6">
                <div class="flex-1 border-t-2 border-gray-200"></div>
                <div class="px-3 text-gray-500 text-sm"><?= t('register_or') ?></div>
                <div class="flex-1 border-t-2 border-gray-200"></div>
            </div>
            <div class="space-y-3">
                <a href="/google-login.php" class="w-full flex items-center justify-center gap-3 bg-white border-2 border-gray-300 text-gray-700 font-medium py-3 px-4 rounded-lg hover:bg-gray-50 transition duration-300">
                    <img src="https://developers.google.com/identity/images/g-logo.png" alt="Google" class="w-5 h-5">
                    <?= t('register_with_google') ?>
                </a>
            </div>
            <div class="text-center">
                <p class="text-gray-600 text-sm">
                    <?= t('register_have_account') ?>
                    <a href="/login" class="text-blue-600 font-semibold hover:text-blue-800 transition">
                        <?= t('register_login') ?>
                    </a>
                </p>
            </div>
        </form>

        <div class="mt-6 text-center text-gray-500 text-xs">
            <p><?= t('register_terms') ?></p>
        </div>
    </div>
</div>

<?php require 'templates/footer.php'; ?>