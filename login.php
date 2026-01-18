<?php
require 'includes/db.php';
require 'includes/auth.php';
require 'includes/i18n.php';
require 'vendor/autoload.php';

if (isLoggedIn()) {
    header('Location: /dashboard');
    exit();
}

$error = '';

// Login manual menggunakan username dan password
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['avatar'] = $user['avatar'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['login_method'] = 'manual';
        header('Location: /dashboard');
        exit();
    } else {
        $error = t('login_error');
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
            <p class="text-gray-600"><?= t('login_title') ?> untuk berbagi meme terbaik</p>
        </div>

        <?php if ($error): ?>
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-600 rounded-lg">
                <p class="text-red-700 flex items-center gap-2">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= htmlspecialchars($error) ?>
                </p>
            </div>
        <?php endif; ?>
        
        <form method="POST" class="bg-white shadow-2xl rounded-xl p-8 space-y-6">
            <div>
                <label for="username" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-user mr-2 text-blue-600"></i><?= t('login_username') ?>
                </label>
                <input 
                    type="text" 
                    name="username" 
                    id="username"
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200 transition" 
                    placeholder="<?= t('login_username') ?>"
                    required
                >
            </div>

            <div>
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-lock mr-2 text-blue-600"></i><?= t('login_password') ?>
                </label>
                <input 
                    type="password" 
                    name="password" 
                    id="password"
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200 transition" 
                    placeholder="<?= t('login_password') ?>"
                    required
                >
            </div>

            <div>
                <button 
                    type="submit" 
                    class="w-full bg-gradient-to-r from-blue-600 to-blue-800 text-white font-bold py-3 px-4 rounded-lg hover:from-blue-700 hover:to-blue-900 transition duration-300 transform hover:scale-105">
                    <i class="fas fa-sign-in-alt mr-2"></i><?= t('login_submit') ?>
                </button>
            </div>

            <div class="flex items-center my-6">
                <div class="flex-1 border-t-2 border-gray-200"></div>
                <div class="px-3 text-gray-500 text-sm">or</div>
                <div class="flex-1 border-t-2 border-gray-200"></div>
            </div>

            <div class="text-center">
                <p class="text-gray-600 text-sm">
                    <?= t('login_no_account') ?>
                    <a href="/register" class="text-blue-600 font-semibold hover:text-blue-800 transition">
                        <?= t('login_register') ?>
                    </a>
                </p>
            </div>
        </form>

        <div class="mt-8 p-4 bg-blue-50 border-l-4 border-blue-600 rounded-lg">
            <p class="text-blue-700 text-sm flex items-start gap-2">
                <i class="fas fa-info-circle mt-1"></i>
                <span>Platform Pixco adalah tempat terbaik untuk berbagi dan menemukan meme lokal yang seru!</span>
            </p>
        </div>
    </div>
</div>

<?php require 'templates/footer.php'; ?>