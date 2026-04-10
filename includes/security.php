<?php
// CSRF Protection and Security Helper Functions

// Generate CSRF token
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Verify CSRF token
function verifyCSRFToken($token) {
    if (empty($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        return false;
    }
    return true;
}

// CSRF token input field
function csrfTokenInput() {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(generateCSRFToken()) . '">';
}

// Sanitize input
function sanitizeInput($input) {
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    return $input;
}

// Validate email
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Validate strong password
function isStrongPassword($password) {
    if (strlen($password) < 8) {
        return false;
    }
    if (!preg_match('/[A-Z]/', $password)) {
        return false;
    }
    if (!preg_match('/[a-z]/', $password)) {
        return false;
    }
    if (!preg_match('/[0-9]/', $password)) {
        return false;
    }
    return true;
}

// Get safe file extension based on MIME type
function getSafeExtension($mimeType, $originalName) {
    $mimeToExt = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp'
    ];
    
    if (!isset($mimeToExt[$mimeType])) {
        return false;
    }
    
    // Optionally, validate that original extension matches
    $originalExt = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    $expectedExt = $mimeToExt[$mimeType];
    
    // Allow common variations
    if ($mimeType === 'image/jpeg' && in_array($originalExt, ['jpg', 'jpeg'])) {
        return $expectedExt;
    }
    
    if ($originalExt === $expectedExt) {
        return $expectedExt;
    }
    
    return false;
}

// Rate limiting helper
function checkRateLimit($key, $max_attempts = 5, $time_window = 300) {
    $cache_key = 'rate_limit_' . $key;
    
    if (!isset($_SESSION[$cache_key])) {
        $_SESSION[$cache_key] = ['attempts' => 0, 'first_attempt' => time()];
    }
    
    $cache = $_SESSION[$cache_key];
    $current_time = time();
    
    // Reset jika melewati time window
    if ($current_time - $cache['first_attempt'] > $time_window) {
        $_SESSION[$cache_key] = ['attempts' => 0, 'first_attempt' => $current_time];
        return true;
    }
    
    // Increment attempts
    $_SESSION[$cache_key]['attempts']++;
    
    return $_SESSION[$cache_key]['attempts'] <= $max_attempts;
}

// Get rate limit remaining
function getRateLimitRemaining($key, $max_attempts = 5) {
    $cache_key = 'rate_limit_' . $key;
    
    if (!isset($_SESSION[$cache_key])) {
        return $max_attempts;
    }
    
    return max(0, $max_attempts - $_SESSION[$cache_key]['attempts']);
}

// 2FA Functions
use PragmaRX\Google2FA\Google2FA;

function generate2FASecret() {
    $google2fa = new Google2FA();
    return $google2fa->generateSecretKey();
}

function get2FAQRCodeUrl($secret, $username, $issuer = 'Pixco') {
    $google2fa = new Google2FA();
    return $google2fa->getQRCodeUrl($issuer, $username, $secret);
}

function verify2FACode($secret, $code) {
    $google2fa = new Google2FA();
    return $google2fa->verifyKey($secret, $code);
}
?>
