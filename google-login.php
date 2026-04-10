<?php
require 'includes/db.php';
require 'includes/auth.php';
require 'includes/i18n.php';
require 'includes/config.php';

require_once 'vendor/autoload.php';

$client = new Google_Client();
$client->setClientId(getConfig('google_client_id'));
$client->setClientSecret(getConfig('google_client_secret'));
$client->setRedirectUri(getConfig('google_redirect_uri', 'http://localhost/google-login.php'));
$client->addScope("email");
$client->addScope("profile");

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token);

    $oauth = new Google_Service_Oauth2($client);
    $google_account_info = $oauth->userinfo->get();

    $google_id = $google_account_info->id;
    $email = $google_account_info->email;
    $name = $google_account_info->name;
    $picture = $google_account_info->picture;

    // Check if user exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        // Login existing user
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['avatar'] = $user['avatar'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['login_method'] = 'google';
    } else {
        // Create new user
        $username = preg_replace('/[^a-zA-Z0-9]/', '', $name) . rand(100, 999);
        
        // Ensure unique username
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        while ($stmt->fetch()) {
            $username = preg_replace('/[^a-zA-Z0-9]/', '', $name) . rand(100, 999);
            $stmt->execute([$username]);
        }

        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, avatar, email_verified, role) VALUES (?, ?, '', ?, TRUE, 'user')");
        $stmt->execute([$username, $email, $picture]);

        $user_id = $pdo->lastInsertId();

        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $username;
        $_SESSION['avatar'] = $picture;
        $_SESSION['role'] = 'user';
        $_SESSION['login_method'] = 'google';
    }

    header('Location: /dashboard');
    exit();
} else {
    // Redirect to Google
    $auth_url = $client->createAuthUrl();
    header('Location: ' . $auth_url);
    exit();
}
?>