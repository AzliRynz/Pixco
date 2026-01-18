<?php
require 'includes/db.php';
require 'includes/auth.php';
require 'includes/i18n.php';

redirectIfNotLoggedIn();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'] ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;
    $avatar = $_FILES['avatar'] ?? null;

    try {
        if ($avatar && $avatar['tmp_name']) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($avatar['type'], $allowed_types)) {
                throw new Exception(t('settings_invalid_image'));
            }

            $avatar_path = 'uploads/avatars/' . $_SESSION['user_id'] . '-' . time() . '.' . pathinfo($avatar['name'], PATHINFO_EXTENSION);
            if (!move_uploaded_file($avatar['tmp_name'], __DIR__ . '/' . $avatar_path)) {
                throw new Exception(t('settings_upload_failed'));
            }

            $stmt = $pdo->prepare("UPDATE users SET avatar = ? WHERE id = ?");
            $stmt->execute([$avatar_path, $_SESSION['user_id']]);
        }

        $stmt = $pdo->prepare("UPDATE users SET email = ?, password = IFNULL(?, password) WHERE id = ?");
        $stmt->execute([$email, $password, $_SESSION['user_id']]);

        $success = t('settings_updated');
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Fetch user data
$stmt = $pdo->prepare("SELECT email, avatar FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

require 'templates/header.php';
?>
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-center mb-6">Pengaturan</h1>
    <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6" role="alert">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php elseif ($success): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data" class="max-w-lg mx-auto bg-white shadow-md rounded px-8 py-6">
        <div class="mb-4">
            <label for="email" class="block text-gray-700 font-bold mb-2">Email</label>
            <input 
                type="email" 
                name="email" 
                id="email" 
                value="<?= htmlspecialchars($user['email']) ?>" 
                class="border border-gray-300 rounded w-full px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                required>
        </div>
        <div class="mb-4">
            <label for="password" class="block text-gray-700 font-bold mb-2">Password Baru (Opsional)</label>
            <input 
                type="password" 
                name="password" 
                id="password" 
                class="border border-gray-300 rounded w-full px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="mb-4">
            <label for="avatar" class="block text-gray-700 font-bold mb-2">Avatar</label>
            <div class="flex items-center space-x-4">
                <img 
                    id="avatarPreview" 
                    src="/<?= $user['avatar'] ? '/' . htmlspecialchars($user['avatar']) : '/default-avatar.png' ?>" 
                    alt="Avatar Preview" 
                    class="w-16 h-16 rounded-full border">
                <input 
                    type="file" 
                    name="avatar" 
                    id="avatar" 
                    accept="image/*" 
                    class="block w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    onchange="previewAvatar(event)">
            </div>
        </div>
        <div>
            <button 
                type="submit" 
                class="w-full bg-indigo-500 text-white font-semibold py-2 px-4 rounded hover:bg-blue-600 transition duration-300">
                Simpan
            </button>
        </div>
    </form>
</div>

<script>
    // Pratinjau Avatar
    function previewAvatar(event) {
        const reader = new FileReader();
        reader.onload = function() {
            const output = document.getElementById('avatarPreview');
            output.src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>

<?php require 'templates/footer.php'; ?>