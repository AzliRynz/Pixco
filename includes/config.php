<?php
// Configuration helper functions

$_config_cache = null;

function getConfig($key = null, $default = null) {
    global $_config_cache;
    
    if ($_config_cache === null) {
        $_config_cache = require __DIR__ . '/../config.php';
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
