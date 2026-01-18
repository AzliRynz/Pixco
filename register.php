<?php
require 'includes/db.php';
require 'includes/auth.php';
require 'includes/i18n.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    // Validation
    if (strlen($username) < 3) {
        $error = t('register_username_min');
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = t('register_email_invalid');
    } elseif (strlen($password) < 6) {
        $error = t('register_password_min');
    } elseif ($password !== $confirmPassword) {
        $error = t('register_password_mismatch');
    } else {
        try {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'user')");
            $stmt->execute([$username, $email, $passwordHash]);
            
            $success = t('register_success');
            header('refresh:2; url=login');
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'username') !== false) {
                $error = t('register_username_taken');
            } elseif (strpos($e->getMessage(), 'email') !== false) {
                $error = t('register_email_taken');
            } else {
                $error = t('register_error_default');
            }
        }
    }
}

require 'templates/header.php';
?>
<div class="min-h-screen flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center gap-2 mb-4">
                <div class="text-5xl text-blue-600"><i class="fas fa-smile"></i></div>
                <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">LokalKu</h1>
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
        
        <form method="POST" class="bg-white shadow-2xl rounded-xl p-8 space-y-6">
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
                    required
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
                    minlength="6"
                    required
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
                    minlength="6"
                    required
                >
            </div>

            <div>
                <button 
                    type="submit" 
                    class="w-full bg-gradient-to-r from-blue-600 to-blue-800 text-white font-bold py-3 px-4 rounded-lg hover:from-blue-700 hover:to-blue-900 transition duration-300 transform hover:scale-105">
                    <i class="fas fa-user-plus mr-2"></i><?= t('register_submit') ?>
                </button>
            </div>

            <div class="flex items-center my-6">
                <div class="flex-1 border-t-2 border-gray-200"></div>
                <div class="px-3 text-gray-500 text-sm"><?= t('register_or') ?></div>
                <div class="flex-1 border-t-2 border-gray-200"></div>
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