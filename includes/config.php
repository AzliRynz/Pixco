<?php
// Configuration helper functions

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$_config_cache = null;

function getConfig($key = null, $default = null) {
    global $_config_cache;
    
    if ($_config_cache === null) {
        $_config_cache = [
            'db_host' => $_ENV['DB_HOST'] ?? 'localhost',
            'db_name' => $_ENV['DB_NAME'] ?? 'pixco',
            'db_user' => $_ENV['DB_USER'] ?? 'root',
            'db_pass' => $_ENV['DB_PASS'] ?? '',
            'smtp_host' => $_ENV['SMTP_HOST'] ?? 'smtp.gmail.com',
            'smtp_username' => $_ENV['SMTP_USERNAME'] ?? '',
            'smtp_password' => $_ENV['SMTP_PASSWORD'] ?? '',
            'smtp_port' => $_ENV['SMTP_PORT'] ?? 587,
            'smtp_from_email' => $_ENV['SMTP_FROM_EMAIL'] ?? 'noreply@pixco.com',
            'smtp_from_name' => $_ENV['SMTP_FROM_NAME'] ?? 'Pixco',
            'google_client_id' => $_ENV['GOOGLE_CLIENT_ID'] ?? '',
            'google_client_secret' => $_ENV['GOOGLE_CLIENT_SECRET'] ?? '',
            'google_redirect_uri' => $_ENV['GOOGLE_REDIRECT_URI'] ?? 'http://localhost/google-login.php',
            'site_name' => $_ENV['SITE_NAME'] ?? 'Pixco',
            'default_language' => $_ENV['DEFAULT_LANGUAGE'] ?? 'id',
            'session_secure' => filter_var($_ENV['SESSION_SECURE'] ?? true, FILTER_VALIDATE_BOOLEAN),
            'session_httponly' => filter_var($_ENV['SESSION_HTTPONLY'] ?? true, FILTER_VALIDATE_BOOLEAN),
            'session_samesite' => $_ENV['SESSION_SAMESITE'] ?? 'Strict',
        ];
    }
    
    if ($key === null) {
        return $_config_cache;
    }
    
    return $_config_cache[$key] ?? $default;
}

function setConfig($key, $value) {
    // Only allow specific config keys to be set
    $allowed_keys = ['site_name', 'default_language'];
    
    if (!in_array($key, $allowed_keys)) {
        throw new Exception("Invalid config key: {$key}");
    }
    
    // Validate values
    if ($key === 'site_name') {
        $value = trim($value);
        if (strlen($value) < 1 || strlen($value) > 50) {
            throw new Exception("Site name must be between 1 and 50 characters");
        }
        // Remove potentially dangerous characters
        $value = preg_replace('/[^a-zA-Z0-9\s\-\.]/u', '', $value);
    } elseif ($key === 'default_language') {
        if (!in_array($value, ['en', 'id'])) {
            throw new Exception("Invalid language: {$value}");
        }
    }
    
    $configFile = __DIR__ . '/../config.php';
    $config = require $configFile;
    
    $config[$key] = $value;
    
    $configContent = "<?php\n// Configuration file for Pixco\n// This file stores default settings for the application\n\nreturn [\n";
    
    foreach ($config as $k => $v) {
        if (is_string($v)) {
            $configContent .= "    '{$k}' => '" . addslashes($v) . "',\n";
        } else {
            $configContent .= "    '{$k}' => {$v},\n";
        }
    }
    
    $configContent .= "];\n?>";
    
    return file_put_contents($configFile, $configContent) !== false;
}

function getSiteName() {
    return getConfig('site_name', 'Pixco');
}

function getDefaultLanguage() {
    return getConfig('default_language', 'id');
}
?>
