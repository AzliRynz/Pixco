<?php
require 'includes/db.php';
require 'includes/auth.php';
require 'includes/i18n.php';
require 'includes/config.php';
require 'includes/security.php';

if (!isset($_SESSION['temp_user_id'])) {
    header('Location: /login');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $error = 'Invalid security token.';
    } else {
        $code = $_POST['code'] ?? '';

        if (empty($code)) {
            $error = '2FA code is required';
        } else {
            $stmt = $pdo->prepare("SELECT twofa_secret FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['temp_user_id']]);
            $user = $stmt->fetch();

            if (verify2FACode($user['twofa_secret'], $code)) {
                // Complete login
                $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
                $stmt->execute([$_SESSION['temp_user_id']]);
                $user = $stmt->fetch();

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['avatar'] = $user['avatar'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['login_method'] = 'manual';

                unset($_SESSION['temp_user_id']);

                header('Location: /dashboard');
                exit();
            } else {
                $error = t('twofa_invalid');
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
                <div class="text-5xl text-blue-600"><i class="fas fa-shield-alt"></i></div>
                <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent"><?= getSiteName() ?></h1>
            </div>
            <p class="text-gray-600">Two-Factor Authentication</p>
        </div>

        <?php if ($error): ?>
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-600 rounded-lg">
                <p class="text-red-700 flex items-center gap-2">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= htmlspecialchars($error) ?>
                </p>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-lg shadow-lg p-8">
            <form method="POST" class="space-y-6">
                <?= csrfTokenInput() ?>

                <div>
                    <label for="code" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-key mr-2 text-blue-600"></i>Enter your 6-digit code
                    </label>
                    <input 
                        type="text" 
                        name="code" 
                        id="code"
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200 transition"
                        maxlength="6"
                        required
                        autocomplete="off">
                    <p class="text-gray-500 text-xs mt-1">Enter the code from your authenticator app</p>
                </div>

                <button 
                    type="submit" 
                    class="w-full bg-gradient-to-r from-blue-600 to-blue-800 text-white font-bold py-3 px-6 rounded-lg hover:from-blue-700 hover:to-blue-900 transition duration-300 flex items-center justify-center gap-2">
                    <i class="fas fa-sign-in-alt"></i>
                    Verify & Login
                </button>
            </form>

            <div class="mt-6 text-center">
                <a href="/login" class="text-blue-600 hover:text-blue-800 text-sm">
                    <i class="fas fa-arrow-left mr-1"></i>Back to Login
                </a>
            </div>
        </div>
    </div>
</div>
<?php
require 'templates/footer.php';
?>