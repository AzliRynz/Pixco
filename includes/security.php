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
?>
