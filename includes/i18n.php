<?php
// Sistem multi-bahasa (i18n)

session_start();

// Load configuration
$config = require __DIR__ . '/../config.php';

// Default language
$supportedLanguages = ['en', 'id'];
$defaultLanguage = $config['default_language'] ?? 'id';

// Get language from session, cookie, or parameter
$lang = $defaultLanguage;

if (isset($_GET['lang']) && in_array($_GET['lang'], $supportedLanguages)) {
    $lang = $_GET['lang'];
    $_SESSION['language'] = $lang;
    setcookie('language', $lang, time() + (86400 * 365), '/');
} elseif (isset($_SESSION['language'])) {
    $lang = $_SESSION['language'];
} elseif (isset($_COOKIE['language']) && in_array($_COOKIE['language'], $supportedLanguages)) {
    $lang = $_COOKIE['language'];
}

$_SESSION['language'] = $lang;

// Load language file
$langFile = __DIR__ . "/../lang/{$lang}.php";

if (file_exists($langFile)) {
    $GLOBALS['lang_strings'] = require $langFile;
} else {
    // Fallback to default language
    $langFile = __DIR__ . "/../lang/{$defaultLanguage}.php";
    $GLOBALS['lang_strings'] = require $langFile;
}

// Helper function to get translated string
function t($key, $default = '') {
    if (isset($GLOBALS['lang_strings'][$key])) {
        return $GLOBALS['lang_strings'][$key];
    }
    return $default ?: $key;
}

// Get current language
function getLang() {
    return $_SESSION['language'] ?? 'id';
}
?>
