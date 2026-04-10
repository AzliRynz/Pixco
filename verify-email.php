<?php
require 'includes/db.php';
require 'includes/auth.php';
require 'includes/i18n.php';
require 'includes/config.php';

$message = '';
$type = 'error';

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email_verification_token = ? AND email_verified = FALSE");
    $stmt->execute([$token]);
    $user = $stmt->fetch();
    
    if ($user) {
        $stmt = $pdo->prepare("UPDATE users SET email_verified = TRUE, email_verification_token = NULL WHERE id = ?");
        $stmt->execute([$user['id']]);
        $message = t('email_verification_success');
        $type = 'success';
    } else {
        $message = t('email_verification_failed');
    }
} else {
    $message = t('email_verification_failed');
}

require 'templates/header.php';
?>
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center gap-2 mb-4">
                <div class="text-5xl text-blue-600"><i class="fas fa-envelope"></i></div>
                <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent"><?= getSiteName() ?></h1>
            </div>
            <p class="text-gray-600">Email Verification</p>
        </div>

        <div class="mb-6 p-4 <?php if ($type === 'success'): ?>bg-green-50 border-l-4 border-green-600<?php else: ?>bg-red-50 border-l-4 border-red-600<?php endif; ?> rounded-lg">
            <p class="<?php if ($type === 'success'): ?>text-green-700<?php else: ?>text-red-700<?php endif; ?> flex items-center gap-2">
                <i class="fas <?php if ($type === 'success'): ?>fa-check-circle<?php else: ?>fa-exclamation-circle<?php endif; ?>"></i>
                <?= htmlspecialchars($message) ?>
            </p>
        </div>

        <div class="text-center">
            <a href="/login" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200">
                <i class="fas fa-sign-in-alt"></i>
                Go to Login
            </a>
        </div>
    </div>
</div>
<?php
require 'templates/footer.php';
?>